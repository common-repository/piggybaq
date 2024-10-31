
jQuery(document).ready(function ($) {
	$(".do_piggybaq").click(function (e) {
		var id = $(this).data("piggybaq-product_id");
		piggybaq_client.do_piggybaq(id);

		e.preventDefault();
	});
});

var piggybaq_client = {
	do_piggybaq: function ( product_id ) {
		
		var redirecturi = "request/" + piggybaq_server.store_id + "/" + product_id;
		window.open(piggybaq_server.service_url + "/user/auth?redirecturi=" + redirecturi, "piggybaq", 
			"scrollbars=no, resizable=no, top=100, left=100, width=788, height=618");

		// jQuery.post( piggybaq_server.ajax_url, data, function(response) {
		// 	console.log(response);
		// });
	}
}