<?php
/**
 * Delete a news
 *
 * $Id: delete_file.php 1186 2009-01-21 10:24:00Z duck $
 *
 * Copyright Obala d.o.o. (www.obala.si)
 *
 * @author  Duck <duck@obala.net>
 * @package News
 */
require_once dirname(__FILE__) . '/lib/base.php';
require_once 'Horde/Variables.php';

if (!Auth::isAdmin('news:admin')) {
    $notification->push(_("Only admin can delete a news."));
    header('Location: ' . Horde::applicationUrl('edit.php'));
    exit;
}

$vars = Variables::getDefaultVariables();
$form = new Horde_Form($vars, _("Are you sure you want to delete file?"), 'delete');
$form->setButtons(array(_("Remove"), _("Cancel")));

$news_id = (int)Util::getFormData('news_id');
$form->addHidden('', 'news_id', 'int', true);

$news_lang = Util::getFormData('lang', News::getLang());
$form->addHidden('', 'news_lang', 'text', false);

$file_id = Util::getFormData('file_id');
$form->addHidden('', 'file_id', 'text', true);

$article = $news->get($news_id);
$files = $news->getFiles($news_id);
foreach ($files as $file) {
    if ($file['file_id'] == $file_id) {
        break;
    }
}

$form->addVariable($file['file_name'], 'file_name', 'description', false);
$form->addVariable(News::format_filesize($file['file_size']), 'file_size', 'description', false);
$form->addVariable($article['title'], 'news', 'description', false);
$form->addVariable($article['content'], 'content', 'description', false);

if ($form->validate()) {

    if (Util::getFormData('submitbutton') == _("Remove")) {
        $result = News::deleteFile($file_id);
        if ($result instanceof PEAR_Error) {

            $notification->push(sprintf(_("Error deleteing file \"%s\" from news \"%s\""), $file_id['file_name'], $article['title']), 'horde.success');

        } else {

            $result = $news->write_db->query('DELETE FROM ' . $news->prefix . '_files WHERE file_id = ?', array($file_id));
            if ($result instanceof PEAR_Error) {
                $notification->push($result);
            }

            $count = $news->db->getOne('SELECT COUNT(*) FROM ' . $news->prefix . '_files WHERE news_id = ?', array($news_id));
            if ($count instanceof PEAR_Error) {
                $notification->push($count);
            }

            $result = $news->write_db->query('UPDATE ' . $news->prefix . ' SET attachments = ? WHERE id = ?', array($count, $news_id));
            if ($result instanceof PEAR_Error) {
                $notification->push($result);
            }

            $notification->push(sprintf(_("File \"%s\" was deleted from news \"%s\""), $file_id['file_name'], $article['title']), 'horde.success');

            $cache->expire('news_'  . $news_lang . '_' . $news_id);
        }

    } else {

        $notification->push(sprintf(_("File \"%s\" was not deleted from news \"%s\""), $file_id['file_name'], $article['title']), 'horde.success');

    }

    header('Location: ' . News::getUrlFor('news', $news_id));
    exit;
}

require NEWS_TEMPLATES . '/common-header.inc';
require NEWS_TEMPLATES . '/menu.inc';

$form->renderActive(null, null, null, 'post');

require $registry->get('templates', 'horde') . '/common-footer.inc';