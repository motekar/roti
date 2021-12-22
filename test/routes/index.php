<?php use Motekar\Roti; ?>

<h1>No Rewrite Router</h1>

<h2>Examples</h2>

<ul>
    <li><a href="<?php echo Roti::url('/about'); ?>">About</a></li>
    <li><a href="<?php echo Roti::url('/page-slug'); ?>">Page Slug</a></li>
    <li><a href="<?php echo Roti::url('/page-123'); ?>">Page Num</a></li>
    <li><a href="<?php echo Roti::url('/page-slug-123'); ?>">Page Slug Num</a></li>
    <li><a href="<?php echo Roti::url('/page-123-slug'); ?>">Page Num Slug</a></li>
    <li><a href="<?php echo Roti::url('/profile'); ?>">/profile</a></li>
    <li><a href="<?php echo Roti::url('/profile/'); ?>">/profile/</a></li>
    <li><a href="<?php echo Roti::url('/profile/test'); ?>">/profile/test</a></li>
    <li><a href="<?php echo Roti::url('/news/2021/12/news-title-or-slug'); ?>">/news/2021/12/news-title-or-slug</a></li>
</ul>
