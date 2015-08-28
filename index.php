<?php
/*
 *  ZX blog
 * 
 *  Copyright (C) 2013-2015 - Andrej Budinčević
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

session_start();
include("inc/blog.php");

$blog = new Blog();
?>
<!DOCTYPE html>
 
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>zx | <?php echo $blog->getPostTitle($_REQUEST['id']); ?></title>

  <script type="text/javascript" src="/js/shCore.js"></script>

  <script type="text/javascript">
    SyntaxHighlighter.defaults['gutter'] = true;
    SyntaxHighlighter.defaults['toolbar'] = false;

    SyntaxHighlighter.all();
  </script>

  <link rel="alternate" type="application/rss+xml" href="http://zx.rs/feed" />

  <link rel='shortcut icon' type='image/x-icon' href='/favicon.ico' />

  <link rel="stylesheet" type="text/css" href="/css/main.css">
  <link rel="stylesheet" type="text/css" href="/css/shCore.css">
  <link rel="stylesheet" type="text/css" href="/css/shThemeRDark.css">
</head>
<body>
  <div id="wrapper">
    <header>
      <a href="http://zx.rs/" class="logo">
        Z
        <span class="blue sub">X</span>
        <span class="desc-top"><span class="blue">c</span>oder<span class="blue">'</span>s</span>
        <span class="desc-bot"><span class="blue">b</span>log</span>
      </a>    
    </header>

    <section id="content">
      <?php
        if(!$_REQUEST['err']) 
          if($_REQUEST['id']) {
            echo $blog->getPost($_REQUEST['id'], $_SESSION['username']);
          } else {
            echo $blog->printPosts();          
          }
        else
          echo $blog->displayMsg($_REQUEST['err']);
      ?>
    </section>

    <section id="sidebar">
      <h3>latest <span class="blue">posts</span></h3>
      <ul id="posts">
        <?php 
          echo $blog->printBlogPosts(); 
        ?>
      </ul>

      <h3 class="about">about <span class="blue">me</span></h3>
      <article class="post">
        Hi, my name is Andrej. <br /><br />I'm a software engineer and hardware enthusiast.<br /><br /> This is my technical blog about all and everything. 
      </article>
    </section>
  </div>
</body>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-62531471-1', 'auto');
  ga('send', 'pageview');

</script>
</html>
