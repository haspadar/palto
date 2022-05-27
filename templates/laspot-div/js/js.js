'use strict';

const myImageSlider = new Swiper('.image-slider', {

	navigation: {
		nextEl: '.swiper-button-next',
		prevEl: '.swiper-button-prev'
	},

	pagination: {
		el: '.swiper-pagination',
		clickable: true,
		dynamicBullets: true,
	},

	simulateTouch: true,
	touchRatio: 1,
	touchAngle: 45,
	grabCursor: true,

	keyboard: {
		enabled: true,
		onlyInViewport: true,
		pageUpDown: true,
	},

	mousewheel: {
		sensitivity: 1,
		eventsTarget: '.image-slider',
	},

	autoHeight: true,
	slidesPerView: 1,
	watchOverflow: true,
	slidesPerGroup: 1,
	initialSlide: 0,
	speed: 500,

});