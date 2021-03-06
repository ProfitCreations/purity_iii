<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::addIncludePath(T3_PATH . '/html/com_content');
JHtml::addIncludePath(dirname(dirname(__FILE__)));
require_once T3_TEMPLATE_PATH . '/helper.php';

// Create shortcuts to some parameters.
$params   = $this->item->params;
$images   = json_decode($this->item->images);
$urls     = json_decode($this->item->urls);
$canEdit  = $params->get('access-edit');
$user     = JFactory::getUser();
$aInfo    = (($params->get('show_author') && !empty($this->item->author)) ||
			($params->get('show_category')) ||
			($params->get('show_create_date')) ||
			($params->get('show_parent_category')) ||
			($params->get('show_publish_date'))) ||
			($params->get('show_modify_date')) ||
			($params->get('show_hits'));

$exAction = ($canEdit ||
			$params->get('show_print_icon') ||
			$params->get('show_email_icon'));

JHtml::_('behavior.caption');
?>

<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header clearfix">
		<h1 class="page-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>

<div class="item-page<?php echo $this->pageclass_sfx ?> clearfix">

<?php if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative) : ?>
	<?php echo $this->item->pagination; ?>
<?php endif; ?>

<!-- Article -->
<article>
<?php if ($params->get('show_title')) : ?>
	<header class="article-header clearfix">
		<h1 class="article-title">
			<?php echo $this->escape($this->item->title); ?>
		</h1>
	</header>
<?php endif; ?>

<?php if ($aInfo || $exAction) : ?>
	<!-- Aside -->
	<aside class="article-aside clearfix">

		<?php if ($aInfo) : ?>
			<?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
		<?php endif; ?>

		<?php if ($exAction) : ?>
			<div class="btn-group pull-right">
				<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#"> <i class="fa fa-cog"></i>
					<span class="caret"></span> </a>
				<ul class="dropdown-menu">
					<?php if (!$this->print) : ?>
						<?php if ($params->get('show_print_icon')) : ?>
							<li class="print-icon"> <?php echo JHtml::_('icon.print_popup', $this->item, $params); ?> </li>
						<?php endif; ?>
						<?php if ($params->get('show_email_icon')) : ?>
							<li class="email-icon"> <?php echo JHtml::_('icon.email', $this->item, $params); ?> </li>
						<?php endif; ?>
						<?php if ($canEdit) : ?>
							<li class="edit-icon"> <?php echo JHtml::_('icon.edit', $this->item, $params); ?> </li>
						<?php endif; ?>
					<?php else : ?>
						<li> <?php echo JHtml::_('icon.print_screen', $this->item, $params); ?> </li>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif; ?>
	</aside>
	<!-- //Aside -->
<?php endif; ?>

<?php if (isset ($this->item->toc)) : ?>
	<?php echo $this->item->toc; ?>
<?php endif; ?>

<?php if ($params->get('show_tags', 1) && !empty($this->item->tags)) : ?>
	<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>

	<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
<?php endif; ?>

<?php if (!$params->get('show_intro')) : ?>
	<?php echo $this->item->event->afterDisplayTitle; ?>
<?php endif; ?>

<?php echo $this->item->event->beforeDisplayContent; ?>

<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position))) || (empty($urls->urls_position) && (!$params->get('urls_position')))): ?>
	<?php echo $this->loadTemplate('links'); ?>
<?php endif; ?>

<?php if ($params->get('access-view')): ?>
	<?php if (isset($images->image_fulltext) && !empty($images->image_fulltext)) : ?>
		<?php
		$imgfloat = (empty($images->float_fulltext)) ? $params->get('float_fulltext') : $images->float_fulltext;
		?>
		<div class="pull-<?php echo htmlspecialchars($imgfloat); ?> item-image article-image article-image-full">
			<img
				<?php if ($images->image_fulltext_caption): ?>
					<?php echo 'class="caption"' . ' title="' . htmlspecialchars($images->image_fulltext_caption) . '"'; ?>
				<?php endif; ?>
				src="<?php echo htmlspecialchars($images->image_fulltext); ?>"
				alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>"/>
		</div>
	<?php endif; ?>

	<?php
	if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative):
		echo $this->item->pagination;
	endif;
	?>

	<section class="article-content clearfix">
		<?php echo $this->item->text; ?>
	</section>

	<?php
	if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative): ?>
		<?php
		echo '<hr class="divider-vertical" />';
		echo $this->item->pagination;
		?>
	<?php endif; ?>

	<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))): ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>

	<?php //optional teaser intro text for guests ?>
<?php elseif ($params->get('show_noauth') == true and  $user->get('guest')) : ?>
	<?php echo $this->item->introtext; ?>
	<?php //Optional link to let them register to see the whole article. ?>
	<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
		$link1 = JRoute::_('index.php?option=com_users&view=login');
		$link = new JURI($link1);
		?>
		<section class="readmore">
			<a href="<?php echo $link; ?>">
						<span>
						<?php $attribs = json_decode($this->item->attribs); ?>
						<?php
						if ($attribs->alternative_readmore == null) :
							echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
						elseif ($readmore = $this->item->alternative_readmore) :
							echo $readmore;
							if ($params->get('show_readmore_title', 0) != 0) :
								echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
							endif;
						elseif ($params->get('show_readmore_title', 0) == 0) :
							echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
						else :
							echo JText::_('COM_CONTENT_READ_MORE');
							echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
						endif; ?>
						</span>
			</a>
		</section>
	<?php endif; ?>
<?php endif; ?>
</article>
<!-- //Article -->

<?php if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative): ?>
	<?php echo $this->item->pagination; ?>
<?php endif; ?>

<?php echo $this->item->event->afterDisplayContent; ?>
</div>

<?php JATemplateHelper::loadModules('after-content', 't3xhtml') ?>