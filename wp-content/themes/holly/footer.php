					<?php global $is_special_page; ?>
					<?php if ($is_special_page): ?>
							</div>
							<?php get_sidebar(); ?>
						</div>
					<?php endif; ?>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<div class="ads-728-center ads-top-margin ads-bottom-margin"><?php buzzmedia_ad_dart(array('728x90'), '728b') ?></div>
				<footer class="page-footer" id="bottom-branding" role="contentinfo">
					<a href="http://www.spinmedia.com/#locations">Contact Information</a> /
					<a href="/terms/">Terms and Conditions</a> /
					<a href="http://www.spinmedia.com/privacy-policy/" target="_blank">Privacy Policy</a>
				</footer>
				<div id="footer">
					<div id="comscore_container">
						<div id="comscore_header_text">
							<span class="bold"> Holly Madison</span>, part of Celebuzz, is a member of Spin Entertainment, a division of SpinMedia<br><span id="comscore_header_links">
								<a onclick="javascript:window.open('http://www.spinmedia.com/#audience'); return false;" href="http://www.spinmedia.com/#audience" rel="nofollow">About <span class="bold">SpinMedia</span></a> | 
								<a onclick="javascript:window.open('http://www.spinmedia.com/#audience'); return false;" href="http://www.spinmedia.com/#audience" rel="nofollow">Advertise</a> | 
								<a onclick="javascript:window.open('http://www.spinmedia.com/#locations'); return false;" href="http://www.spinmedia.com/#locations" rel="nofollow">Contact</a> | 
								<a onclick="javascript:window.open('http://www.spinmedia.com/privacy-policy/'); return false;" href="http://www.spinmedia.com/privacy-policy/" rel="nofollow">Privacy Policy</a> | 
								<a onclick="javascript:window.open('http://www.spinmedia.com/advertise/about-our-ads/'); return false;" href="http://www.spinmedia.com/advertise/about-our-ads/" rel="nofollow" class="cs_aboutBug"><img src="http://cdn.buzznet.com/assets/pages/p3/footer/collisionadmarkerwht.png" alt="adChoices" style="margin-bottom: -3px; width: 77px; height: 15px;" height="15" width="77"></a> | 
								<a onclick="javascript:window.open('http://spinmedia.com/copyright/'); return false;" href="http://spinmedia.com/copyright/" rel="nofollow">Copyright</a>
							</span>
						</div>
						<div id="comscore_body_text">
							<div>
								<span class="bold">
									SpinMedia <span class="bold">Publishers</span>: 

									<?php
									$comscore_menu = array(
										'menu'			=> 'ITS_Links',
										'container'		=> false,
										'menu_id'		=> 'comscore_site_list',
										'echo'			=> true,
										'fallback_cb'	=> 'wp_page_menu',
										'after'			=> ' | ',
										'items_wrap'	=> '%3$s',
										'depth'			=> 0
									);
									wp_nav_menu($comscore_menu);
									?>

									<a onclick="javascript:window.open('http://www.spinmedia.com/properties/'); return false;" href="http://www.spinmedia.com/properties/" rel="nofollow">More...</a>

								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php wp_footer(); ?>
	</body>
</html>
