    <div class="footer">
        <div class="copyright">
            <p>Copyright © Designed &amp; Developed by <a href="../index.htm" target="_blank">DexignLab</a> 2021</p>
        </div>
    </div>
</div>

	<script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
	<script src="{{ asset('assets/vendor/chart.js/Chart.bundle.min.js') }}"></script>
	<script src="{{ asset('assets/js/plugins-init/chartjs-init.js') }}"></script>
	
	<script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>
	
	<script src="{{ asset('assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js') }}"></script>
	
	<script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/select2-init.js') }}"></script>

	<script src="{{ asset('assets/vendor/apexchart/apexchart.js') }}"></script>
	
	<script src="{{ asset('assets/vendor/peity/jquery.peity.min.js') }}"></script>
	<script src="{{ asset('assets/js/dashboard/dashboard-1.js') }}"></script>
	
	<script src="{{ asset('assets/vendor/owl-carousel/owl.carousel.js') }}"></script>

	<script src="{{ asset('assets/vendor/moment/moment.min.js') }}"></script>

	<script src="{{ asset('assets/vendor/fullcalendar/js/main.min.js') }}"></script>
	<script src="{{ asset('assets/js/plugins-init/calendar.js') }}"></script>
	
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
	<script src="{{ asset('assets/js/dlabnav-init.js') }}"></script>
	<script src="{{ asset('assets/js/demo.js') }}"></script>

	<script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/sweetalert.init.js') }}"></script>

    <!-- <script src="{{ asset('assets/js/styleSwitcher.js') }}"></script> -->
	<script>
		function cardsCenter()
		{
			jQuery('.card-slider').owlCarousel({
				loop:true,
				margin:0,
				nav:true,
				//center:true,
				slideSpeed: 3000,
				paginationSpeed: 3000,
				dots: true,
				navText: ['<i class="fas fa-arrow-left"></i>', '<i class="fas fa-arrow-right"></i>'],
				responsive:{
					0:{
						items:1
					},
					576:{
						items:1
					},	
					800:{
						items:1
					},			
					991:{
						items:1
					},
					1200:{
						items:1
					},
					1600:{
						items:1
					}
				}
			})
		}
		
		jQuery(window).on('load',function(){
			setTimeout(function(){
				cardsCenter();
			}, 1000); 
		});
		
	</script>

</body>
</html>