<?php
/**
 * @package		sliComments
 * @subpackage	Akismet Plugin
 * @license		GNU General Public License version 3; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSlicommentsMailnew extends JPlugin {
  public function __construct($subject, $config) {
    parent::__construct($subject, $config);
    $this->loadLanguage();
  }

  public function onAfterSaveComment($comment, $error) {
    if ($error == null && $comment->status >= -1) {
      try {
        JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
        $model = JModelLegacy::getInstance('Article', 'ContentModel');
        $article = $model->getItem($comment->article_id);

        $mailer = JFactory::getMailer();
        $config = JFactory::getConfig();

        $mailto = $this->params->get('mailto');
        if (empty($mailto)) {
          $mailto = $config->getValue('config.mailfrom');
        }

        $sender = array( 
          $config->getValue('config.mailfrom'),
          $config->getValue('config.fromname')
        );
        $mailer->setSender($sender);
        $mailer->addRecipient($mailto);
        $body = JText::sprintf(
          'PLG_SLICOMMENTS_MAILNEW_BODY',
          $article->title,
          $comment->article_id
        );
        $mailer->setSubject(JText::_('PLG_SLICOMMENTS_MAILNEW_SUBJECT'));
        $mailer->setBody($body);
        $send = $mailer->Send();
        if ($send !== true) {
          if (JDEBUG) {
            JError::raiseWarning(500, 'Error sending email: '.$send->message);
          }
        } else {
          //echo 'Mail sent';
        }
      } catch (Exception $e) {
        if (JDEBUG) JError::raiseWarning(500, $e->getMessage());
        return;
      }
    }
    return true;
  }
}

