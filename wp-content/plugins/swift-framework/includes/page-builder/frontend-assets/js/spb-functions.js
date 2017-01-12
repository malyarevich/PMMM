/*global jQuery,Vivus,ajaxurl */

// USE STRICT
"use strict";

var SPB = SPB || {};

(function(){

	////////////////////////////////////////////
	// GENERAL
	/////////////////////////////////////////////
 	SPB.general = {
 		init: function() {

 			// Check Element Titles
 			SPB.general.checkElemTitle();

 			// Initiate row functionality
 			SPB.general.initRows();

 			// Init full width elements
 			SPB.general.initFullWidthElements();

			// Check for offsets	
			jQuery('.spb-col-custom-offset').each(function() {
				var parentRow = jQuery(this).parents('.spb-row').first();
				if ( !parentRow.hasClass('has-offset-elements') ) {
					parentRow.addClass('has-offset-elements');
				}
				var parentSectionRow = jQuery(this).parents('section.row').first();
				if ( !parentSectionRow.hasClass('has-offset-elements') ) {
					parentSectionRow.addClass('has-offset-elements');
				}
			});


			// var _animationsList = [];
		 //    var _animationSequenceTimer;
		 //    var $items = jQuery('.onScrollEnter');

		 //    $items.each(function(i, elem){
		 //        var $this = jQuery(this);

		 //        var $watcher = scrollMonitor.create($this.get(0));
		 //        //console.log('scrollwatcher', $watcher, -(window.innerHeight*.2));

		 //        $watcher.enterViewport(function(){

		 //            _animationsList.push(elem);
		 //            clearTimeout(_animationSequenceTimer);
		 //            _animationSequenceTimer = setTimeout(function() {
		 //                _triggerAnimationSequence(_animationsList);
		 //                _animationsList = [];
		 //                scrollMonitor.recalculateLocations();
		 //            }, 10);

		 //        });// enterViewport
		 //    });// each()

		 //    function _triggerAnimationSequence(elems){
			//     //elems = elems.sort(function(a, b) {
			//     //    var aID = a.attr('id');
			//     //    var bID = b.attr('id');
			//     //    return (aID == bID) ? 0 : (aID > bID) ? 1 : -1;
			//     //});
			//     //TweenMax.staggerTo(elems.reverse(), .75,{autoAlpha:1, y:0, ease:Quart.easeOut},.12);
			// }// _triggerAnimationSequence
 		},
 		load: function() {
 			// OFFSET CALC
			SPB.general.offsetCalc();
			SPB.var.window.smartresize( function() {
				SPB.general.offsetCalc();
			});
 		},
 		offsetCalc: function() {
 			var adjustment = 0;
			
			if (jQuery('#wpadminbar').length > 0) {
				adjustment = jQuery('#wpadminbar').height();
				SPB.var.wpadminbarheight = jQuery('#wpadminbar').height();
			}
			
			if (SPB.var.body.hasClass('sticky-header-enabled') && !SPB.var.body.hasClass('sticky-header-transparent') ) {
				adjustment += jQuery('.sticky-header').height() > 0 ? jQuery('.sticky-header').height() : jQuery('#header-section').height();
			}

			if (jQuery('.sticky-top-bar').length > 0) {
				adjustment += jQuery('.sticky-top-bar').height() > 0 ? jQuery('.sticky-top-bar').height() : jQuery('#top-bar').height();
			}
			
			SPB.var.offset = adjustment;
 		},
 		checkElemTitle: function() {
 			jQuery('.title-wrap').each(function() {
 				var thisTitle = jQuery(this);
 				if ( thisTitle.find('.carousel-arrows').length > 0 ) {
 					thisTitle.addClass('has-arrows');
 				}
 			});
 		},
 		initFullWidthElements: function() {
 			var fullWidthElements = [];

 			jQuery('.spb-full-width-element').each(function() {
 				var spbFwElem = {};
 				var element = jQuery(this);

 				// Set vars
				spbFwElem.element = element;
				spbFwElem.isFullWidth = true;
 				// if ( element.parents('.spb-row').length > 0 ) {
 				// spbFwElem.isFullWidth = false;
 				// }
 				if ( spbFwElem.isFullWidth ) {
 					element.parent().addClass('has-fw-elements');
 				}
				spbFwElem.isVideo = element.find('video.parallax-video').length > 0 ? true : false;
				spbFwElem.isParallax = element.hasClass('spb-row-parallax') ? true : false;

				// Add to array
				fullWidthElements.push(spbFwElem);
 			});

 			jQuery.each(fullWidthElements, function(index, elem) {
 				if ( elem.isFullWidth ) {
					SPB.general.fullWidthRow( elem.element, false, true );
				}
				if ( elem.isParallax ) {
 					SPB.general.parallaxRow( elem.element );
 				}
				if ( elem.isVideo ) {
 					if ( SPB.var.isMobile ) {
 						elem.element.find('video').remove();
 					} else {
	 					var videoRow = elem.element;
	 					SPB.general.resizeVideoRow( videoRow );
	 					videoRow.find('video').get(0).addEventListener('loadeddata', function() {
						   SPB.general.resizeVideoRow( videoRow );
						}, false);
 					}
 				}
			});
			
			SPB.var.window.on("throttledresize", function() {
				jQuery.each(fullWidthElements, function(index, elem ) {
					if ( elem.isFullWidth ) {
						SPB.general.fullWidthRow( elem.element, false, true );
					}
					if ( elem.isParallax ) {
 						SPB.general.parallaxRow( elem.element );
 					}
					if ( elem.isVideo && !SPB.var.isMobile ) {
 						SPB.general.resizeVideoRow( elem.element );
 					}
				});
			});
 		},
 		initRows: function() {
 			// Loop through each row and add to a cached array
 			jQuery('.spb-row').each(function() {
 				var spbRow = {};
 				var element = jQuery(this);

 				// Set vars
 				spbRow.element = element;
 				spbRow.isParallax = element.hasClass('spb-row-parallax') ? true : false;
 				spbRow.isFullWidth = element.data('wrap') === "full-width-contained" || element.data('wrap') === "full-width-stretch" ? true : false;
 				spbRow.isFullWidthStretch = element.data('wrap') === "full-width-stretch" ? true : false;
 				spbRow.isVideo = element.data('row-type') === "video" ? true : false;
 				spbRow.isWindowHeight = element.data('row-height') === "window-height" ? true : false;
 				spbRow.columnEqualHeights = element.data('col-equal-heights') === true ? true : false;
 				spbRow.columnContentPosition = element.data('data-col-content-pos') !== '' ? true : false;
 				
 				// Add to array
 				SPB.var.pageRows.push(spbRow);
 			});

 			// For each row, run necessary functions
 			jQuery.each(SPB.var.pageRows, function(index, row) {
 				if ( row.isParallax ) {
 					SPB.general.parallaxRow( row.element );
 				}
 				if ( row.isFullWidth ) {
 					SPB.general.fullWidthRow( row.element, true, row.isFullWidthStretch );
 				}
 				if ( row.isVideo ) {
 					if ( SPB.var.isMobile ) {
 						row.element.find('video').remove();
 					} else {
	 					var videoRow = row.element;
	 					SPB.general.resizeVideoRow( videoRow );
	 					videoRow.find('video').get(0).addEventListener('loadeddata', function() {
						   SPB.general.resizeVideoRow( videoRow );
						}, false);
 					}
 				}
 				if ( row.isWindowHeight ) {
 					SPB.general.windowHeightRow( row.element );
 				}
 				if ( row.columnEqualHeights ) {
 					SPB.general.columnEqualHeightsRow( row.element );
 				}

 				// Run when imagesLoaded
 				row.element.imagesLoaded( function() {
				  	if ( row.columnContentPosition ) {
 						SPB.general.columnContentPositionRow( row.element );
 					}
				});
 			});

 			jQuery('.spb-column-container.spb-row-parallax').each(function() {
 				SPB.general.parallaxRow( jQuery(this) );
 			})

 			// On window load, run necessary functions
			jQuery(window).load( function() {
				jQuery.each(SPB.var.pageRows, function(index, row) {
					setTimeout( function() {
						if ( row.columnEqualHeights ) {
 							SPB.general.columnEqualHeightsRow( row.element );
 						}
					}, 200);
	 			});
			});

 			// On window resize, run necessary functions
			SPB.var.window.on("throttledresize", function() {
				jQuery.each(SPB.var.pageRows, function(index, row) {
					if ( row.isParallax ) {
 						SPB.general.parallaxRowResize( row.element );
 					}
	 				if ( row.isFullWidth ) {
	 					SPB.general.fullWidthRow( row.element, true, row.isFullWidthStretch );
	 				}
	 				if ( row.isVideo && !SPB.var.isMobile ) {
 						SPB.general.resizeVideoRow( row.element );
 					}
 					if ( row.columnContentPosition ) {
 						SPB.general.columnContentPositionRow( row.element );
 					}
	 			});

	 			jQuery('.spb-column-container.spb-row-parallax').each(function() {
 					SPB.general.parallaxRow( jQuery(this) );
 				})
			});



			// Check for expanding row
			if ( jQuery('.spb-row-expanding').length > 0 ) {
				SPB.general.expandingRow();
			}
 		},
 		expandingRow: function() {
			jQuery(document).on('click', '.spb-row-expand-text', function(e) {
				e.preventDefault();
				var expand = jQuery(this),
					expandRow = expand.next();

				if (expandRow.hasClass('spb-row-expanding-open') && !expandRow.hasClass('spb-row-expanding-active')) {
					expandRow.addClass('spb-row-expanding-open').addClass('spb-row-expanding-active').slideUp(800);
					setTimeout(function() {
						expand.removeClass('row-open').find('span').text(expand.data('closed-text'));
						expandRow.css('display', 'block').removeClass('spb-row-expanding-open').removeClass('spb-row-expanding-active');
					}, 800);
				} else if (!expandRow.hasClass('spb-row-expanding-active')) {
					expand.addClass('row-open').find('span').text(expand.data('open-text'));
					expandRow.css('display', 'none').addClass('spb-row-expanding-open').addClass('spb-row-expanding-active').slideDown(800);
					setTimeout(function() {
						expandRow.removeClass('spb-row-expanding-active');
					}, 800);
				}

			});
		},
 		parallaxRow: function( rowInstance ) {

 			// resize bg image
 			SPB.general.parallaxRowResize( rowInstance );

 			// setup parallax
 			var speed = 0.2;
 			if ( rowInstance.data('parallax-speed') === "fast" ) {
 				speed = 0.4;
 			} else if ( rowInstance.data('parallax-speed') === "slow" ) {
 				speed = 0.1;
 			}
 			rowInstance.parallax( '50%', speed, false );

 		},
 		parallaxRowResize: function( rowInstance ) {
 			var speed = 0.2;
 			if ( rowInstance.data('parallax-speed') === "fast" ) {
 				speed = 0.4;
 			} else if ( rowInstance.data('parallax-speed') === "slow" ) {
 				speed = 0.1;
 			}
 			var newHeight = Math.ceil( SPB.var.window.height() * speed + rowInstance.outerHeight(true) );
 			var marginTop = (newHeight - rowInstance.height()) / 4;
		 	rowInstance.find('.spb-row-parallax-layer').css({
		 		'margin-top' : '-' + marginTop + 'px',
		 		'height' : newHeight
		 	});
 		},
		fullWidthRow: function( element, isRow, contentStretch ) {

        	// Setup variables
			var row, sizer, sizerOffset, contentInner;
			if ( isRow ) {
            	row = element.parent('.fw-row');
            	sizer = row.next(".spb-row-sizer") ? row.next(".spb-row-sizer") : row.parent().find(".spb-row-sizer");
            	contentInner = element.children('.spb_content_element');
            	sizerOffset = sizer.offset().left;
            } else {
            	row = element;
            	sizer = row.next(".spb-fw-sizer") ? row.next(".spb-fw-sizer") : row.parent().find(".spb-fw-sizer");
            	sizerOffset = sizer.offset().left;
            }
            
            // Hide row
            row.addClass("spb-hidden");

            // Caclulations
            var marginLeft = parseInt( row.css("margin-left"), 10 ),
                marginRight = parseInt( row.css("margin-right"), 10 ),
                offset = Math.floor( 0 - sizerOffset - marginLeft ),
                width = Math.floor( SPB.var.window.width() );
            
            if ( SPB.var.body.hasClass('layout-boxed') ) {
            	width = Math.ceil( jQuery('#container').width() );
            	offset = offset + jQuery('#container').offset().left;
            } else if ( SPB.var.body.hasClass('vertical-header') ) {
            	width = Math.ceil( jQuery('#main-container').width() );
            	offset = offset + jQuery('#main-container').offset().left;
            } else if ( SPB.var.body.hasClass('hero-content-split') || SPB.var.body.hasClass('boxed-inner-page') ) {
            	width = Math.ceil( jQuery('.inner-page-wrap').parent().outerWidth() );
            	offset = offset + jQuery('.inner-page-wrap').parent().offset().left;
            }

            // Apply styles
            row.css({
                position: "relative",
                left: SPB.var.body.hasClass('rtl') ? -offset : offset,
                width: width
            });
            row.addClass('fw-row-adjusted');

            // Apply inner padding if not stretched
            if ( !contentStretch && contentInner ) {
                var padding = -1 * offset;
                if ( padding < 0 ) {
                	padding = 0;
                }
	            var paddingRight = width - padding - sizer.width() + marginLeft + marginRight;
	            if ( paddingRight < 0 ) {
                	paddingRight = 0;
                }
                contentInner.css({
                    "padding-left": padding + "px",
                    "padding-right": paddingRight + "px"
                });
            }

            // Set as initiated
            element.attr( "data-sb-init", "true" );
            
            // Unhide row
            element.css('opacity', 1).css('visibility', 'visible');
            row.removeClass("spb-hidden");
        },
        resizeVideoRow: function( element ) {
        	if ( element.find('video').length === 0 ) {
        		return;
        	}
        	var video = element.find('video'),
				assetHeight = element.outerHeight(),
				assetWidth = element.outerWidth(),
				videoWidth = video[0].videoWidth,
				videoHeight = video[0].videoHeight;

			// Use the largest scale factor of horizontal/vertical
			var scale_h = assetWidth / videoWidth;
			var scale_v = assetHeight / videoHeight;
			var scale = scale_h > scale_v ? scale_h : scale_v;

			// Update minium width to never allow excess space
			var min_w = videoWidth/videoHeight * (assetHeight+20);

			// Don't allow scaled width < minimum video width
			if (scale * videoWidth < min_w) {scale = min_w / videoWidth;}

			// Scale the video
			video.width(Math.ceil(scale * videoWidth +2));
			video.height(Math.ceil(scale * videoHeight +50));
			video.css('margin-top', - (video.height() - assetHeight) /2);
			video.css('margin-left', - (video.width() - assetWidth) /2);
        },
		windowHeightRow: function( element ) {
            var windowHeight = SPB.var.window.height();
            element.css("min-height", windowHeight);
        },
        columnEqualHeightsRow: function( element ) {
        	if ( SPB.var.window.width() >= 768 ) {
        		element.find('> .spb_content_element .spb-column-container').matchHeight();
        	} else {
        		element.find('> .spb_content_element .spb-column-container').css('min-height', '');
        	}
        },
        columnContentPositionRow: function( element ) {
        	var contentPosition = element.data('col-content-pos');
        	if ( typeof contentPosition === "undefined" || contentPosition === 'top' ) {
        		return;
        	}
        	element.addClass('spb-hidden');
			if (element.find('.spb-column-inner,.spb-row-multi-col').length > 0) {
				element.find('.spb-column-inner > .row,.spb-row-multi-col > .row').each(function() {
					var columnInner = jQuery(this),
						contentHeight = 0;

					if (columnInner.find('> div').length > 1) {

						columnInner.addClass('multi-column-row');

						columnInner.find('> div').each(function() {
							var assetPadding = parseInt(jQuery(this).css('padding-top')) + parseInt(jQuery(this).css('padding-bottom')),
								itemHeight = jQuery(this).find('.spb-asset-content').first().innerHeight() + assetPadding;
							if (itemHeight > contentHeight) {
								contentHeight = itemHeight;
							}
						});

						// SET THE ROW & INNER ASSET MIN HEIGHT
						columnInner.css('min-height', contentHeight);
						columnInner.find('> div').css('min-height', contentHeight);

						// VERTICAL ALIGN THE INNER ASSET CONTENT
						columnInner.find('> div').each(function() {
							jQuery(this).addClass('spb-hidden');
							var assetContent = jQuery(this).find('.spb-asset-content').first(),
								assetPadding = parseInt(jQuery(this).css('padding-top')) + parseInt(jQuery(this).css('padding-bottom')) + parseInt(assetContent.css('padding-top')) + parseInt(assetContent.css('padding-bottom')),
								innerHeight = assetContent.height() + assetPadding,
								margins = Math.floor((contentHeight / 2) - (innerHeight /2));

							if ( margins > 0 ) {
								if ( contentPosition === 'center' ) {
									assetContent.css('margin-top', margins).css('margin-bottom', margins);
								} else if ( contentPosition === 'bottom' ) {
									assetContent.css('margin-top', margins*2);
								}
							} else {
								assetContent.css('margin-top', '').css('margin-bottom', '');
							}
							jQuery(this).removeClass('spb-hidden');
						});

					}
				});
			}
			element.removeClass('spb-hidden');
        }
 	};

 	/////////////////////////////////////////////
	// GENERAL ASSETS
	/////////////////////////////////////////////
	SPB.assets = {
		init: function() {
			if (jQuery('.chart-shortcode').length > 0) {
			SPB.assets.chartAssets();	
			}
			if (jQuery('.sf-count-asset').length > 0) {
			SPB.assets.countAssets();
			}
			if (jQuery('.sf-countdown').length > 0) {
			SPB.assets.countdownAssets();
			}
			if (jQuery('.sf-image-banner').length > 0) {
			SPB.assets.imageBanners();
			}
		},
		load: function() {
			if (jQuery('.chart-shortcode').length > 0) {
			SPB.assets.animateCharts();	
			}
		},
		chartAssets: function() {
			jQuery('.chart-shortcode').each(function(){
				jQuery(this).easyPieChart({
					animate: 1000,
					lineCap: 'round',
					lineWidth: jQuery(this).attr('data-linewidth'),
					size: jQuery(this).attr('data-size'),
					barColor: jQuery(this).attr('data-barcolor'),
					trackColor: jQuery(this).attr('data-trackcolor'),
					scaleColor: 'transparent'
				});
			});
        },
        animateCharts: function() {
			jQuery('.chart-shortcode').each(function(){
				var chart = jQuery(this);
				chart.appear(function() {
					if (!jQuery(this).hasClass('animated')) {
						jQuery(this).addClass('animated');
						var animatePercentage = parseInt(jQuery(this).attr('data-animatepercent'), 10);
						jQuery(this).data('easyPieChart').update(animatePercentage);
					}
				});
			});
		},
		countAssets: function() {
			jQuery('.sf-count-asset').each(function() {

				var countAsset = jQuery(this),
					countNumber = countAsset.find('.count-number'),
					countDivider = countAsset.find('.count-divider').find('span'),
					countSubject = countAsset.find('.count-subject');

				countNumber.fitText(0.4, { minFontSize: '16px', maxFontSize: '62px' });

				if ( !SPB.var.isMobile ) {
					countAsset.appear(function() {

						countNumber.countTo({
							onComplete: function () {
								if ( !countDivider.hasClass('icon-divide') ) {
									countDivider.animate({
										'width': 80
									}, 400, 'easeOutCubic');
								}
								countSubject.delay(100).animate({
									'opacity' : 1,
									'bottom' : '0px'
								}, 600, 'easeOutCubic');
							}
						});

					}, {accX: 0, accY: -150}, 'easeInCubic');
				} else {
					countNumber.countTo({
						onComplete: function () {
							if ( !countDivider.hasClass('icon-divide') ) {
								countDivider.animate({
									'width': 50
								}, 400, 'easeOutCubic');
							}
							countSubject.delay(100).animate({
								'opacity' : 1,
								'bottom' : '0px'
							}, 600, 'easeOutCubic');
						}
					});
				}

			});
		},
		countdownAssets: function() {
			jQuery('.sf-countdown').each(function() {
				var countdownInstance = jQuery(this),
					year = parseInt(countdownInstance.data('year'), 10),
					month = parseInt(countdownInstance.data('month'), 10),
					day = parseInt(countdownInstance.data('day'), 10),
					countdownDate = new Date(year, month - 1, day);

				var labelStrings = jQuery('#countdown-locale'),
					pluralLabels = [labelStrings.data('label_years'),labelStrings.data('label_months'),labelStrings.data('label_weeks'),labelStrings.data('label_days'),labelStrings.data('label_hours'),labelStrings.data('label_mins'),labelStrings.data('label_secs')],
					singularLabels = [labelStrings.data('label_year'),labelStrings.data('label_month'),labelStrings.data('label_week'),labelStrings.data('label_day'),labelStrings.data('label_hour'),labelStrings.data('label_min'),labelStrings.data('label_sec')];

				countdownInstance.countdown({
					until: countdownDate,
					since: null,
					labels: pluralLabels,
					labels1: singularLabels,
					onExpiry: function() {
						setTimeout(function() {
							countdownInstance.fadeOut(500);
						}, 1000);
					}
				});
			});
		},
		imageBanners: function() {
			jQuery('.sf-image-banner').each(function() {
				jQuery(this).find('.image-banner-content').vCenter();
			});
		}
	};

	/////////////////////////////////////////////
	// ANIMATED HEADLINE
	/////////////////////////////////////////////
 	SPB.animatedHeadline = {
		init: function () {
			var animatedHeadlines = jQuery('.spb-animated-headline'),
				animationDelay = 2500;

			animatedHeadlines.each( function() {
				var headline = jQuery(this).find('.sf-headline');

				setTimeout( function() {
					SPB.animatedHeadline.animateHeadline( headline );
				}, animationDelay);
			});

			// Single letter animation
			SPB.animatedHeadline.singleLetters( jQuery('.sf-headline.letters').find('b') );
		},
		singleLetters: function ( $words ) {
			$words.each( function() {
				var word = jQuery(this),
					letters = word.text().split(''),
					selected = word.hasClass('is-visible');

				for ( var i in letters ) {
					if ( word.parents('.rotate-2').length > 0 ) letters[i] = '<em>' + letters[i] + '</em>';
					letters[i] = ( selected ) ? '<i class="in">' + letters[i] + '</i>': '<i>' + letters[i] + '</i>';
				}

			    var newLetters = letters.join('');
			    word.html( newLetters ).css( 'opacity', 1 );
			});
		},
		animateHeadline: function ( $headlines ) {
			var duration = 2500;

			$headlines.each( function() {
				var headline = jQuery(this);
				
				if ( headline.hasClass('loading-bar') ) {
					duration = 3800;
					var barAnimationDelay = 3800,
						barWaiting = barAnimationDelay - 3000;
					setTimeout( function() {
						headline.find('.sf-words-wrapper').addClass('is-loading');
					}, barWaiting);
				} else if ( headline.hasClass('clip') ) {
					var spanWrapper = headline.find('.sf-words-wrapper'),
						newWidth = spanWrapper.width() + 10;
					spanWrapper.css('width', newWidth);
				} else if ( !headline.hasClass('type') ) {
					//assign to .sf-words-wrapper the width of its longest word
					var words = headline.find('.sf-words-wrapper b'),
						width = 0;
					words.each( function() {
						var wordWidth = jQuery(this).width();
					    if (wordWidth > width) width = wordWidth;
					});
					width = width > 0 ? width : '';
					headline.find('.sf-words-wrapper').css('width', width);
				}

				//trigger animation
				setTimeout( function() {
					SPB.animatedHeadline.hideWord( headline.find('.is-visible').eq(0) );
				}, duration);
			});
		},
		hideWord: function ( $word ) {
			var nextWord = SPB.animatedHeadline.takeNext( $word ),
				animationDelay = 2500,
				lettersDelay = 50,
				typeLettersDelay = 150,
				selectionDuration = 500,
				typeAnimationDelay = selectionDuration + 800,
				revealDuration = 600,
				barAnimationDelay = 3800,
				barWaiting = barAnimationDelay - 3000;

			if ( $word.parents('.sf-headline').hasClass('type') ) {
				var parentSpan = $word.parent('.sf-words-wrapper');
				parentSpan.addClass('selected').removeClass('waiting');	
				setTimeout( function() { 
					parentSpan.removeClass('selected'); 
					$word.removeClass('is-visible').addClass('is-hidden').children('i').removeClass('in').addClass('out');
				}, selectionDuration);
				setTimeout( function() {
					SPB.animatedHeadline.showWord( nextWord, typeLettersDelay );
				}, typeAnimationDelay);
			} else if ( $word.parents('.sf-headline').hasClass('letters') ) {
				var bool = ( $word.children('i').length >= nextWord.children('i').length ) ? true : false;
				SPB.animatedHeadline.hideLetter( $word.find('i').eq(0), $word, bool, lettersDelay );
				SPB.animatedHeadline.showLetter( nextWord.find('i').eq(0), nextWord, bool, lettersDelay );
			}  else if ( $word.parents('.sf-headline').hasClass('clip') ) {
				$word.parents('.sf-words-wrapper').animate({ width : '2px' }, revealDuration, function(){
					SPB.animatedHeadline.switchWord( $word, nextWord );
					SPB.animatedHeadline.showWord( nextWord );
				});
			} else if ( $word.parents('.sf-headline').hasClass('loading-bar') ) {
				$word.parents('.sf-words-wrapper').removeClass('is-loading');
				SPB.animatedHeadline.switchWord($word, nextWord);
				setTimeout( function() {
					SPB.animatedHeadline.hideWord( nextWord );
				}, barAnimationDelay);
				setTimeout( function() {
					$word.parents('.sf-words-wrapper').addClass('is-loading');
				}, barWaiting);
			} else {
				SPB.animatedHeadline.switchWord( $word, nextWord );
				setTimeout( function() {
					SPB.animatedHeadline.hideWord( nextWord );
				}, animationDelay);
			}
		},
		showWord: function ( $word, $duration ) {
			var revealDuration = 600,
				revealAnimationDelay = 1500;

			if ( $word.parents('.sf-headline').hasClass('type') ) {
				SPB.animatedHeadline.showLetter( $word.find('i').eq(0), $word, false, $duration );
				$word.addClass('is-visible').removeClass('is-hidden');
			} else if ( $word.parents('.sf-headline').hasClass('clip') ) {
				$word.parents('.sf-words-wrapper').animate({
					'width' : $word.width() + 10
				}, revealDuration, function() { 
					setTimeout( function() {
						SPB.animatedHeadline.hideWord( $word );
					}, revealAnimationDelay); 
				});
			}
		},
		hideLetter: function ( $letter, $word, $bool, $duration ) {
			var animationDelay = 2500;

			$letter.removeClass('in').addClass('out');
			
			if ( !$letter.is(':last-child') ) {
			 	setTimeout( function() {
			 		SPB.animatedHeadline.hideLetter( $letter.next(), $word, $bool, $duration );
			 	}, $duration);  
			} else if ( $bool ) { 
			 	setTimeout( function() {
			 		SPB.animatedHeadline.hideWord( SPB.animatedHeadline.takeNext( $word ) );
			 	}, animationDelay);
			}

			if ( $letter.is(':last-child') && jQuery('html').hasClass('no-csstransitions') ) {
				var nextWord = SPB.animatedHeadline.takeNext( $word );
				SPB.animatedHeadline.switchWord( $word, nextWord );
			} 
		},
		showLetter: function ( $letter, $word, $bool, $duration ) {
			var animationDelay = 2500;

			$letter.addClass('in').removeClass('out');
			
			if ( !$letter.is(':last-child') ) { 
				setTimeout( function() {
					SPB.animatedHeadline.showLetter( $letter.next(), $word, $bool, $duration );
				}, $duration ); 
			} else { 
				if ( $word.parents('.sf-headline').hasClass('type') ) {
					setTimeout( function() {
						$word.parents('.sf-words-wrapper').addClass('waiting');
					}, 200);
				}
				if ( !$bool ) {
					setTimeout( function() {
						SPB.animatedHeadline.hideWord( $word );
					}, animationDelay);
				}
			}
		},
		takeNext: function ( $word ) {
			return ( !$word.is(':last-child') ) ? $word.next() : $word.parent().children().eq(0);
		},
		takePrev: function ( $word ) {
			return ( !$word.is(':first-child') ) ? $word.prev() : $word.parent().children().last();
		},
		switchWord: function ( $oldWord, $newWord ) {
			$oldWord.removeClass('is-visible').addClass('is-hidden');
			$newWord.removeClass('is-hidden').addClass('is-visible');
		}
	};

	/////////////////////////////////////////////
	// DIRECTORY USER LISTINGS
	/////////////////////////////////////////////
	SPB.directoryUserListings = {
		init: function() {
			// CANCEL LISTING MODAL
			jQuery( document ).on('click', '.cancel-listing-modal', function() {
			    jQuery( '.spb-modal-listing' ).html( '' );
        		jQuery( '.spb-modal-listing ' ).hide();
        	    jQuery( '#spb_edit_listing' ).hide();
        		return false;
			});
            			
            // SAVE LISTING MODAL
			jQuery( document ).on('click', '.save-listing-modal', function() {
				jQuery('#add-directory-entry').submit();
		    });

			// EDIT LISTING
	        jQuery( 'body' ).append( '<div id="spb_edit_listing"></div><div class="spb-modal-listing"></div>' );

            jQuery( document ).on('click', '.edit-listing', function() {

				var ajaxurl = jQuery('.user-listing-results').attr('data-ajax-url');
				var listing_id = jQuery(this).attr('data-listing-id');
				var data = {
				    action: 'sf_edit_directory_item',
				    listing_id: listing_id
				};

				jQuery.post(ajaxurl, data, function( response ) {
					jQuery( '#spb_edit_listing' ).show().css( {"padding-top": 60} );
					jQuery( '.spb-modal-listing' ).html( response );
					jQuery( '.spb-modal-listing' ).show();
					jQuery( '#spb_edit_listing' ).html( '' );
				});
							
				return false;

			});

			// Delete listing confirm
			jQuery( document ).on('click', '.delete-listing-confirmation', function(e) {				
				e.preventDefault();
				var ajaxurl = jQuery('.user-listing-results').attr('data-ajax-url');
				var listing_id = jQuery('#modal-from-dom').attr('listing-id');	   
				var data = {
					action: 'sf_delete_directory_item',
					listing_id: listing_id
				};
				jQuery.post( ajaxurl, data, function() {
				    location.reload();
				});
			});

			// Cancel the delete listing confirmation popup 	
			jQuery( document ).on('click', '.cancel-delete-listing', function(e) {
				e.preventDefault();
				jQuery('#modal-from-dom').modal('hide');
			});

			// Displays the delete listing confirmation popup	
			jQuery( document ).on( 'click', '.delete-listing', function(e) {
				e.preventDefault();
				var listing_id = jQuery(this).attr('data-listing-id');
				jQuery('#modal-from-dom').attr('listing-id', listing_id);
				jQuery('#modal-from-dom').data('id', listing_id).modal('show');
			});
		}
	};

	/////////////////////////////////////////////
	// DYNAMIC HEADER
	/////////////////////////////////////////////
	SPB.dynamicHeader = {
		init: function () {
			var headerHeight = jQuery('.header-wrap').height();

			if ( !SPB.var.body.hasClass('sticky-header-transparent') ) {
				return;
			}
			SPB.var.window.scroll(function() {
				var inview = jQuery('.dynamic-header-change:in-viewport');
				var scrollTop = SPB.var.window.scrollTop() + headerHeight;

				if ( inview.length > 0 ) {
					inview.each(function() {
						var thisSection = jQuery(this),
							thisStart = thisSection.offset().top,
							thisEnd = thisStart + thisSection.outerHeight(),
							headerStyle = thisSection.data('header-style');

						//console.log('scrollTop: '+scrollTop+', start: '+thisStart+', end: '+thisEnd+', style:'+headerStyle)
						
						if ( scrollTop < thisStart || scrollTop > thisEnd ) {
							return;
						}

						if ( headerStyle === "" && SPB.var.defaultHeaderStyle !== "" ) {
							jQuery('.header-wrap').attr('data-style', SPB.var.defaultHeaderStyle);
						}

						if ( scrollTop > thisStart && scrollTop < thisEnd ) {
							jQuery('.header-wrap').attr('data-style', headerStyle);
						}
					});
				}
			});
		}
	};


	/////////////////////////////////////////////
	// ISOTOPE ASSET
	/////////////////////////////////////////////
 	SPB.isotopeAsset = {
		init: function () {
			jQuery('.spb-isotope').each(function() {
				var isotopeInstance = jQuery(this),
					layoutMode = isotopeInstance.data('layout-mode');

				isotopeInstance.isotope({
					resizable: true,
					layoutMode: layoutMode,
					isOriginLeft: !SPB.var.isRTL
				});
				setTimeout(function() {
					isotopeInstance.isotope('layout');
				}, 500);
			});	
		}
	};


	/////////////////////////////////////////////
	// FAQs
	/////////////////////////////////////////////
 	SPB.faqs = {
		init: function () {
			jQuery('.faq-item').on('click', function() {
				var faqItem = jQuery(this);
				faqItem.toggleClass('closed');
				faqItem.find('.faq-text').slideToggle(400);
			});
		}
	};


	/////////////////////////////////////////////
	// ICON BOX GRID
	/////////////////////////////////////////////
 	SPB.iconBoxGrid = {
		init: function () {
			jQuery(document).on('click', '.spb_icon_box_grid a.box-link', function(e) {
				var linkHref = jQuery(this).attr('href'),
					linkOffset = jQuery(this).data('offset') ? jQuery(this).data('offset') : 0;

				if (linkHref && linkHref.indexOf('#') === 0) {
					var headerHeight = SPB.var.offset;

					SPB.var.isScrolling = true;

					jQuery('html, body').stop().animate({
						scrollTop: jQuery(linkHref).offset().top - headerHeight + linkOffset
					}, 1000, 'easeInOutExpo', function() {
						SPB.var.isScrolling = false;
					});

					e.preventDefault();

				} else {
					return e;
				}
			});
		}
	};


	/////////////////////////////////////////////
	// MULTILAYER PARALLAX FUNCTIONS
	/////////////////////////////////////////////

	SPB.mlparallax = {
		init: function() {
			jQuery('.spb_multilayer_parallax').each(function() {

				var mlParallaxAsset = jQuery(this),
					xScalar = mlParallaxAsset.data('xscalar'),
					yScalar = mlParallaxAsset.data('xscalar'),
					windowHeight = parseInt(SPB.var.window.height(), 10),
					assetFullscreen = mlParallaxAsset.data('fullscreen'),
					assetHeight = parseInt(mlParallaxAsset.data('max-height'), 10);

				// Asset Height
				if (jQuery('#wpadminbar').length > 0) {
					windowHeight = windowHeight - jQuery('#wpadminbar').height();
				}
				if (assetFullscreen) {
					assetHeight = windowHeight;
				} else {
					assetHeight = windowHeight > assetHeight ? assetHeight : windowHeight;
				}

				// Set up once images loaded
				mlParallaxAsset.imagesLoaded(function () {

					SPB.mlparallax.setContentLayerPos(mlParallaxAsset);

					mlParallaxAsset.mlparallax({
						scalarX: xScalar,
						scalarY: yScalar
					});

					mlParallaxAsset.animate({
						'opacity' : 1,
						'height' : assetHeight
					}, 400);
				});

			});
		},
		setContentLayerPos: function(mlParallaxAsset) {
			mlParallaxAsset.find('.content-layer').each(function() {
				var contentLayer = jQuery(this);
				contentLayer.vCenter();
			});
		}
	};


	/////////////////////////////////////////////
	// SVG ICON ANIMATE
	/////////////////////////////////////////////
 	SPB.svgIconAnimate = {
		init: function () {
			jQuery('.sf-svg-icon-animate').each(function() {
				var thisSVG = jQuery(this),
					svg_id = thisSVG.attr('id'),
					file_url = thisSVG.data('svg-src'),
					anim_type = thisSVG.data('anim-type');
					//path_timing = thisSVG.data('path-timing'),
					//anim_timing = thisSVG.data('anim-timing');

				if ( thisSVG.hasClass('animation-disabled') ) {
					new Vivus(svg_id, {
							duration: 1,
							file: file_url,
							type: anim_type,
							selfDestroy: true,
							onReady: function(svg) {
								svg.finish();
							}
						});
				} else {
					new Vivus(svg_id, {
						duration: 200,
						file: file_url,
						type: anim_type,
						pathTimingFunction: Vivus.EASE_IN,
						animTimingFunction: Vivus.EASE_OUT,
					});
					setTimeout(function() {
						thisSVG.css('opacity', 1);
					}, 50);
				}
			});
		}
	};


	/////////////////////////////////////////////
	// TEAM MEMBER AJAX
	/////////////////////////////////////////////
 	SPB.teamMemberAjax = {
		init: function () {
			
			jQuery(document).on( 'click', '.team-member-ajax', function(e) {

				if ( SPB.var.isMobile || SPB.var.window.width() < 1000 ) {
					return e;
				}

				e.preventDefault();

				// Add body classes
			    SPB.var.body.addClass( 'sf-team-ajax-will-open' );
			    SPB.var.body.addClass( 'sf-container-block sf-ajax-loading' );

			    // Fade in overlay
			    jQuery('.sf-container-overlay').animate({
			    	'opacity' : 1
			    }, 300);

				// Run ajax post
				var postID = jQuery(this).data('id');
				jQuery.post( ajaxurl, {
			        action: 'spb_team_member_ajax',            
			        post_id: postID // << should grab this from input...
			    }, function(data) {
			        var response   =  jQuery(data);
			        var postdata   =  response.filter('#postdata').html();
			        
			        SPB.var.body.append( '<div class="sf-team-ajax-container"></div>' );
			        jQuery( '.sf-team-ajax-container' ).html(postdata);

			        setTimeout(function() {
			        	jQuery( '.sf-container-overlay' ).addClass('loading-done');
			        	SPB.var.body.addClass( 'sf-team-ajax-open' );
			        	jQuery('.sf-container-overlay').on( 'click touchstart', SPB.teamMemberAjax.closeOverlay );
			        }, 300);
			    });
			});

			jQuery(document).on( 'click', '.team-ajax-close', function(e) {
				e.preventDefault();
				SPB.teamMemberAjax.closeOverlay();
			});
		},
		closeOverlay: function() {
			SPB.var.body.removeClass( 'sf-team-ajax-open' );
			jQuery( '.sf-container-overlay' ).off( 'click touchstart' ).animate({
				'opacity' : 0
			}, 500, function() {
				SPB.var.body.removeClass( 'sf-container-block' );
				SPB.var.body.removeClass( 'sf-team-ajax-will-open' );
				jQuery( '.sf-team-ajax-container' ).remove();
	        	jQuery( '.sf-container-overlay' ).removeClass('loading-done');
			});
		}
	};


	/////////////////////////////////////////////
	// GLOBAL VARIABLES
	/////////////////////////////////////////////
	SPB.var = {};
	SPB.var.window = jQuery(window);
	SPB.var.body = jQuery('body');
	SPB.var.isRTL = SPB.var.body.hasClass('rtl') ? true : false;
	SPB.var.deviceAgent = navigator.userAgent.toLowerCase();
	SPB.var.isMobile = SPB.var.deviceAgent.match(/(iphone|ipod|ipad|android|iemobile)/);
	SPB.var.isIEMobile = SPB.var.deviceAgent.match(/(iemobile)/);
	SPB.var.isSafari = navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1 &&  navigator.userAgent.indexOf('Android') == -1;
	SPB.var.isFirefox = navigator.userAgent.indexOf('Firefox') > -1;
	SPB.var.defaultHeaderStyle = jQuery('.header-wrap').data('default-style');
	SPB.var.isScrolling = false;
	SPB.var.offset = 0;
	SPB.var.wpadminbarheight = 32;

	SPB.var.pageRows = [];

	/////////////////////////////////////////////
	// DOCUMENT READY
	/////////////////////////////////////////////
	SPB.onReady = {
		init: function() {

			SPB.general.init();

			SPB.assets.init();

			// DIRECTORY USER LISTINGS
			if ( jQuery('.spb_directory_user_listings_widget').length > 0 ) {
				SPB.directoryUserListings.init();
			}

			// FAQs
			if ( jQuery('.spb_faqs_element').length > 0 ) {
				SPB.faqs.init();
			}

			// ICON GRID
			if ( jQuery('.spb_icon_box_grid').length > 0 ) {
				SPB.iconBoxGrid.init();
			}

			// SVG ICON ANIMATE
			if ( jQuery('.sf-svg-icon-animate').length > 0 ) {
				SPB.svgIconAnimate.init();
			}

			// DYNAMIC HEADER
			if ( SPB.var.body.hasClass('sticky-header-transparent') ) {
				SPB.dynamicHeader.init();
			}

			// ISOTOPE ASSETS
			if ( jQuery('.spb-isotope').length > 0 ) {
				SPB.isotopeAsset.init();
			}

			// MULTILAYER PARALLAX ASSETS
			if (jQuery('.spb_multilayer_parallax').length > 0) {
				SPB.mlparallax.init();
			}
		}
	};


	/////////////////////////////////////////////
	// DOCUMENT LOAD
	/////////////////////////////////////////////
	SPB.onLoad = {
		init: function() {

			SPB.general.load();

			SPB.assets.load();

			if ( jQuery('.spb-animated-headline').length > 0 ) {
				SPB.animatedHeadline.init();
			}

			if ( jQuery('.team-member-ajax').length > 0 ) {
				SPB.teamMemberAjax.init();
			}
		}
	};


	/////////////////////////////////////////////
	// HOOKS
	/////////////////////////////////////////////
	jQuery(document).ready(SPB.onReady.init);
	jQuery(window).load(SPB.onLoad.init);

})(jQuery);

/////////////////////////////////////////////
// THROTTLED RESIZE
/////////////////////////////////////////////
(function(d){var c=d.event,a,e={_:0},f=0,g,b;a=c.special.throttledresize={setup:function(){d(this).on("resize",a.handler)},teardown:function(){d(this).off("resize",a.handler)},handler:function(k,h){var j=this,i=arguments;g=true;if(!b){setInterval(function(){f++;if(f>a.threshold&&g||h){k.type="throttledresize";c.dispatch.apply(j,i);g=false;f=0}if(f>9){d(e).stop();b=false;f=0}},30);b=true}},threshold:0}})(jQuery);