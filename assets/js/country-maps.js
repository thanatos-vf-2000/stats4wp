function defered(method) {
	if (window.jQuery && window.jQuery.fn.vectorMap) {
		method();
	} else {
		setTimeout(
			function () {
				defered( method );
			},
			50
		);
	}
}

defered(
	function () {
		console.log( "jQuery is now loaded" );

		jQuery(
			function ($) {
				$( '#world-map' ).vectorMap(
					{
						map: 'world_mill',
						series: {
							regions: [{
								values: stats4wpData.gdpData,
								scale: ['#C8EEFF', '#0071A4'],
								normalizeFunction: 'polynomial'
							}]
						},
						onRegionTipShow: function (e, el, code) {
							el.html( el.html() + ' (' + stats4wpData.regionText + ' - ' + (stats4wpData.gdpData[code] || 0) + ')' );
						}
					}
				);
			}
		);
	}
);
