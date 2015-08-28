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

error_reporting(E_STRICT);

class Blog {

	public $location = "change_me"; 
	public $db;


	/* Initialization */

	function __construct() {
		$this->db = new SQLite3($this->location);
	}

	function __destruct() {
		$this->db->close();
	}

	/* Error and informational messages */

	public $title = array(
		1 => "Not found!",
		2 => "Not found!",
		404 => "Not found!",
		403 => "Forbidden access!",
		500 => "Server error!"
	);

	public $message = array(
		1 => "Post not found!",
		2 => "No blog posts found!",
		404 => "The file you were looking for is not available!",
		403 => "Access to the file you were looking for is forbidden!",
		500 => "A server error occured, please <a href=\"/contact/\">contact</a> the administrator!"
	);


	public function displayMsg($code) {
		$result .= "<article class=\"post\">";
		$result .= "<h2>" . $this->title[$code] . "</h2>";
		$result .= stripslashes($this->message[$code]);
		$result .= "</article>";

		return $result;
	}

	/* Utility methods */

	private function getCleanTitle($title) {
		return str_replace(array(" ", "+", "#", ":", "?"), array("-", "-plus", "-hash", "", ""), $title);
	}

	
	/* RSS generation */

	public function generateRSS() {
		$result .= "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		$result .= "<rss version=\"2.0\">";
		$result .= "<channel>";
		$result .= "<title>zx | coder's blog</title>
					<link>http://zx.rs/</link>
					<description>Software engineer's technical blog about all and everything.</description>";
		$results = $this->db->query('SELECT id, post, author, title, time FROM blog ORDER BY id DESC');
		while ($row = $results->fetchArray()) {
			$result .=  "
			<item>
			<link>http://zx.rs/" . $row[0] . "/" . $this->getCleanTitle($row[3]) . "/</link>
			<guid>http://zx.rs/" . $row[0] . "/" . $this->getCleanTitle($row[3]) . "/</guid>
			<title>" . $row[3] . "</title>
			</item>";
		}
		$results->finalize();
		$result .=  "</channel>";
		$result .=  "</rss>";

		return $result;
	}

	/* Blog */

	public function printBlogPosts() {
		$results = $this->db->query('SELECT id,title,author FROM blog ORDER BY id DESC LIMIT 0,5');

		while ($row = $results->fetchArray()) 
			$result .= "<li id=\"" . $row[0] . "\" class=\"post\"><a href=\"/" . $row[0] . "/" . $this->getCleanTitle($row[1]) . "/\" title=\"" . $row[1] . "\">" . $row[1] . "</a></li>";
		
		$results->finalize();

		return $result;
	}

	public function getPostTitle($id) {
		if(isset($id) && !empty($id)) {
			$stmt = $this->db->prepare('SELECT title FROM blog WHERE id=:id');
			$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
			$results = $stmt->execute();
			$row = $results->fetchArray();

			$results->finalize();
			$stmt->close();

			return $row[0]; 
		} else {
			return "coder's blog";
		}
	}

	public function getPost($id, $username) {
		$stmt = $this->db->prepare('SELECT id,post,author,title,COUNT(*),time FROM blog WHERE id = :id');
		$stmt->bindValue(':id', $id, SQLITE3_INTEGER);

		$results = $stmt->execute();

		$row = $results->fetchArray();
		if($row[4] > 0) {
			$result .= "<article class=\"post\" id=\"post_" . $row[0] . "\">";
			$result .= "<h2><a href=\"/" . $row[0] . "/" . $this->getCleanTitle($row[3]) . "/\">" . $row[3] . "</a></h2>";
			$result .= stripslashes($row[1]);
			$result .= $this->share($_SERVER['REQUEST_URI']) . "</article>";
			$result .= "<script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
							<!-- zx -->
							<ins class=\"adsbygoogle\"
							     style=\"display:block\"
							     data-ad-client=\"ca-pub-8015697629980731\"
							     data-ad-slot=\"5332175609\"
							     data-ad-format=\"auto\"></ins>
							<script>
							(adsbygoogle = window.adsbygoogle || []).push({});
							</script><br />";
		} else {
			$result = $this->displayMsg(1);
		}
		$results->finalize();
		$stmt->close();

		return $result;
	}

	public function printPosts() {
		$stat = $this->db->prepare('SELECT COUNT(*) FROM blog');
		$count = $stat->execute();
		$countrow = $count->fetchArray();
		if($countrow[0] > 0) {
			$num = "3";
			$numPages = ceil($countrow[0]/$num);

			$page = empty($_GET['strana']) || $_GET['strana'] > $numPages ? 1 : $_GET['strana'];

			$prev = $page == 1 ? 1 : $page-1;
			$next = $page == $numPages ? $numPages : $page+1;

			$stmt = $this->db->prepare('SELECT id,post,author,title,time FROM blog ORDER BY id DESC LIMIT ' . ($page-1)*$num . ',' . $num . '');
			$results = $stmt->execute();

			while ($row = $results->fetchArray()) {
				$result .= "<article class=\"post\" id=\"post_" . $row[0] . "\">";
				$result .= "<h2><a href=\"/" . $row[0] . "/" . $this->getCleanTitle($row[3]) . "/\">" . $row[3] . "</a></h2>";
				$result .= stripslashes($row[1]);
				$result .= "</article>";
				$result .= "<script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
							<!-- zx -->
							<ins class=\"adsbygoogle\"
							     style=\"display:block\"
							     data-ad-client=\"ca-pub-8015697629980731\"
							     data-ad-slot=\"5332175609\"
							     data-ad-format=\"auto\"></ins>
							<script>
							(adsbygoogle = window.adsbygoogle || []).push({});
							</script><br />";
			}

			$results->finalize();
			$count->finalize();
			$stmt->close();

			if($numPages > 1) {
				$result .= "<div class=\"pagination\"><a href='/1/'>|<</a>";
				$result .= "<a href='/" . $prev . "/'><</a>";

				for($i = 1; $i <= $numPages; $i++) 
					$result .= "<a href='/" . $i . "/' " . ($i == $page ? "class=\"active\"" : "") . ">" . $i . "</a>";

				$result .= "<a href='/" . $next . "/'>></a>";
				$result .= "<a href='/" . $numPages . "/'>>|</a></div>";
			}
		} else {
			$result .= $this->displayMsg(2);
		}

		return $result;
	}

	/* Social integration */

	public function share($url) {
		 return "<div class=\"share\">
			<a title=\"Share on Twitter!\" href=\"https://twitter.com/intent/tweet?hashtags=zx&amp;text=Check+out+this+article&amp;url=http%3A%2F%2Fzx.rs%2F" . urlencode($url) . "\" onclick=\"javascript:window.open(this.href,
                '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;\">tweet <span class=\"blue\">it</span></a>&nbsp;&nbsp;|
              &nbsp;&nbsp;<a title=\"Share on Facebook!\" href=\"https://www.facebook.com/sharer/sharer.php?app_id=1458943427691030&amp;sdk=joey&amp;u=http%3A%2F%2Fzx.rs%2F" . urlencode($url) . "&amp;display=popup\" onclick=\"javascript:window.open(this.href,
                '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;\">share <span class=\"blue\">it</span></a>&nbsp;&nbsp;|
              &nbsp;&nbsp;<a title=\"Share on Google+!\" href=\"https://plus.google.com/share?url=http%3A%2F%2Fzx.rs%2F" . urlencode($url) . "\" onclick=\"javascript:window.open(this.href,
                '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;\">+1 <span class=\"blue\">it</span></a></div>";
	}
}
?>
