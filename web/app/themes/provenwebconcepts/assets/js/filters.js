jQuery(document).ready(function ($) {

	$(".traces-list__filters select, .traces-list__filters input[type=\"search\"]").on("change", function () {
		const $this = $(this),
			filterContainer = $this.closest(".traces-list__filters"),
			selectFields = filterContainer.find("select"),
			search = filterContainer.find("input[type=\"search\"]"),
			aside = $this.closest('.traces-list'),
			content = aside.find('.traces-list__traces .traces-list__content');

		let filters = {};

		$.each(selectFields, function () {

			if (!filters[$(this).attr("name")]) {
				filters[$(this).attr("name")] = [];
			}

			filters[$(this).attr("name")].push($(this).val());
		});

		if(search.val()){
			filters['s'] = search.val();
		}

		$.ajax({
			url: "/wp-json/traces/filters",
			method: "POST",
			data: filters,
			beforeSend: function(){
				content.html('').addClass('is-loading');
			},
			success: function (response) {
				content.html(response.html).removeClass('is-loading');
			},
		});

		return false;

	});

});