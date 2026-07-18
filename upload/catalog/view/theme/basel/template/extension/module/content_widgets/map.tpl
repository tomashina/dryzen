<div id="gmap" class="map-widget"></div>
<script>
$(document).ready(function() {
new Maplace({
	locations: [
		{
		lat: <?php echo $basel_map_lat; ?>,
		lon: <?php echo $basel_map_lon; ?>,
		}
	],
	controls_on_map: true,
	start: 1,
	map_options: {
		zoom: 15,
		scrollwheel: false}
}).Load(); 
});
</script>