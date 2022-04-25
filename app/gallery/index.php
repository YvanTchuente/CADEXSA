<?php require_once dirname(__DIR__) . '/config/index.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="CADEXSA Gallery pictures">
	<meta name="author" content="Yvan Tchuente">
	<title>CADEXSA - Gallery</title>
	<?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
</head>

<body>
	<div id="loader">
		<div>
			<div class="spinner"></div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/header.php"; ?>
	<!-- Page Content -->
	<div class="page-content">
		<div class="page-header">
			<div class="ws-container">
				<h1>Gallery</h1>
			</div>
		</div>
		<div class="ws-container">
			<!-- Gallery article search start -->
			<div class="filter-area">
				<form id="news-filter">
					<div class="nice-select" id="nice-select-1">
						<span class="current" onclick="openSelect(event,'nice-select-1')">month</span>
						<ul class="dropdown">
							<li class="selected">month</li>
							<li>January</li>
							<li>February</li>
							<li>March</li>
							<li>April</li>
							<li>May</li>
							<li>June</li>
							<li>July</li>
							<li>August</li>
							<li>September</li>
							<li>October</li>
							<li>November</li>
							<li>December</li>
						</ul>
						<select id="select-month" name="month" required>
							<option value="" selected>Month</option>
							<option value="January">January</option>
							<option value="February">February</option>
							<option value="March">March</option>
							<option value="April">April</option>
							<option value="May">May</option>
							<option value="June">June</option>
							<option value="July">July</option>
							<option value="August">August</option>
							<option value="September">September</option>
							<option value="October">October</option>
							<option value="November">November</option>
							<option value="December">December</option>
						</select>
					</div>
					<div class="nice-select" id="nice-select-2">
						<span class="current" onclick="openSelect(event,'nice-select-2')">year</span>
						<ul class="dropdown">
							<li class="selected">year</li>
							<li>2021</li>
							<li>2022</li>
						</ul>
						<select id="select-year" name="year" required>
							<option value="" selected>Year</option>
							<option value="2022">2022</option>
							<option value="2021">2021</option>
						</select>
					</div>
					<button type="submit">filter</button>
				</form>
			</div>
			<!-- Gallery article search end -->
			<div class="gallery-wrapper">
				<div class="gallery-item">
					<img src="/static/images/gallery/student.jpg" alt="" />
					<div class="gallery-hvr-wrap">
						<div class="gallery-hvr-desc">
							<h6>Lorem ipsm dolor sitg amet</h6>
							<p>27 Dec 2021</p>
						</div>
						<a href="/static/images/gallery/student.jpg" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
					</div>
				</div>
				<div class="gallery-item">
					<img src="/static/images/gallery/img12.jpg" alt="" />
					<div class="gallery-hvr-wrap">
						<div class="gallery-hvr-desc">
							<h6>Lorem ipsm dolor sitg amet</h6>
							<p>27 Dec 2021</p>
						</div>
						<a href="/static/images/gallery/12.jpg" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
					</div>
				</div>
				<div class="gallery-item">
					<img src="/static/images/gallery/img.jpg" alt="" />
					<div class="gallery-hvr-wrap">
						<div class="gallery-hvr-desc">
							<h6>Lorem ipsm dolor sitg amet</h6>
							<p>27 Dec 2021</p>
						</div>
						<a href="/static/images/gallery/img.jpg" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
					</div>
				</div>
				<div class="gallery-item">
					<img src="/static/images/gallery/img9.jpg" alt="" />
					<div class="gallery-hvr-wrap">
						<div class="gallery-hvr-desc">
							<h6>Lorem ipsm dolor sitg amet</h6>
							<p>27 Dec 2021</p>
						</div>
						<a href="/static/images/gallery/im9.jpg" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
					</div>
				</div>
				<div class="gallery-item">
					<img src="/static/images/gallery/students.jpg" alt="" />
					<div class="gallery-hvr-wrap">
						<div class="gallery-hvr-desc">
							<h6>Lorem ipsm dolor sitg amet</h6>
							<p>27 Dec 2021</p>
						</div>
						<a href="/static/images/gallery/img5.jpg" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
					</div>
				</div>
				<div class="gallery-item">
					<img src="/static/images/gallery/img7.jpg" alt="" />
					<div class="gallery-hvr-wrap">
						<div class="gallery-hvr-desc">
							<h6>Lorem ipsm dolor sitg amet</h6>
							<p>27 Dec 2021</p>
						</div>
						<a href="/static/images/gallery/img7.jpg" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
					</div>
				</div>
				<div class="gallery-item">
					<img src="/static/images/gallery/img14.jpg" alt="" />
					<div class="gallery-hvr-wrap">
						<div class="gallery-hvr-desc">
							<h6>Lorem ipsm dolor sitg amet</h6>
							<p>27 Dec 2021</p>
						</div>
						<a href="/static/images/gallery/img14.jpg" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
					</div>
				</div>
				<div class="gallery-item">
					<img src="/static/images/gallery/img11.jpg" alt="" />
					<div class="gallery-hvr-wrap">
						<div class="gallery-hvr-desc">
							<h6>Lorem ipsm dolor sitg amet</h6>
							<p>27 Dec 2021</p>
						</div>
						<a href="/static/images/gallery/im11.jpg" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
					</div>
				</div>
				<div class="gallery-item">
					<img src="/static/images/gallery/group.jpg" alt="" />
					<div class="gallery-hvr-wrap">
						<div class="gallery-hvr-desc">
							<h6>Lorem ipsm dolor sitg amet</h6>
							<p>27 Dec 2021</p>
						</div>
						<a href="/static/images/gallery/img8.jpg" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
					</div>
				</div>
				<div class="gallery-item">
					<img src="/static/images/gallery/img2.jpg" alt="" />
					<div class="gallery-hvr-wrap">
						<div class="gallery-hvr-desc">
							<h6>Lorem ipsm dolor sitg amet</h6>
							<p>27 Dec 2021</p>
						</div>
						<a href="/static/images/gallery/img8.jpg" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
					</div>
				</div>
				<div class="gallery-item">
					<img src="/static/images/gallery/img10.jpg" alt="" />
					<div class="gallery-hvr-wrap">
						<div class="gallery-hvr-desc">
							<h6>Lorem ipsm dolor sitg amet</h6>
							<p>27 Dec 2021</p>
						</div>
						<a href="/static/images/gallery/img10.jpg" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
					</div>
				</div>
				<div class="gallery-item">
					<img src="/static/images/gallery/img15.jpg" alt="" />
					<div class="gallery-hvr-wrap">
						<div class="gallery-hvr-desc">
							<h6>Lorem ipsm dolor sitg amet</h6>
							<p>27 Dec 2021</p>
						</div>
						<a href="/static/images/gallery/im15.jpg" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
					</div>
				</div>
			</div>
			<div class="pagination-area">
				<ul class="pagination">
					<li class="page-item disabled"><a href="#" class="page-link"><span class="fas fa-angle-double-left"></span></a></li>
					<li class="page-item active"><a href="#" class="page-link">1</a></li>
					<li class="page-item"><a href="#" class="page-link">2</a></li>
					<li class="page-item"><a href="#" class="page-link">3</a></li>
					<li class="page-item"><a href="#" class="page-link">4</a></li>
					<li class="page-item"><a href="#" class="page-link">5</a></li>
					<li class="page-item"><a href="#" class="page-link"><span class="fas fa-angle-double-right"></span></a></li>
				</ul>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>