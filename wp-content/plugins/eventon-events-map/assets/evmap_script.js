/**
 * Javascript for event map
 * @version  1.3
 * @Updated  2016
 */
jQuery(document).ready(function($){
	var geocoder;
	var map;
	var bounds, markerCluster;
	var MAPmarkers = [];
	var LOCATIONSARRAY = [],
			langlong = [];

	// INITIATION
		function initializer(map_canvas_id,mapformat, scrollwheel){			
		    geocoder = new google.maps.Geocoder();
			bounds = new google.maps.LatLngBounds ();

			// default lat long			
				mapData = $('#'+map_canvas_id).siblings('.evoGEO_locations');
				dlat = mapData.data('dlat');
				dlon = mapData.data('dlon');
		    
		    var latlng = new google.maps.LatLng(dlat, dlon);
		    
		   	if(scrollwheel=='false' ){
				var myOptions = {			
					center: latlng,	
					mapTypeId:mapformat,	
					zoom: 8,	
					scrollwheel: false,
				}
			}else{
				var myOptions = {	
					center: latlng,	
					mapTypeId:mapformat,	
					zoom: 8 }
			}
			map = new google.maps.Map(document.getElementById(map_canvas_id), myOptions);
			markerCluster = new MarkerClusterer(map);
		}
	
	// Initial Variables
		var infowindow = new google.maps.InfoWindow();
		var ibTXT ='';
		var address_pos = 0;
		var timeout = 200;

	// event map
		$('.ajde_evcal_calendar.eventmap').each(function(){
			var locations = parseInt($(this).find('.evoGEO_locations').attr('data-count'));
							
			var cal_id = $(this).attr('id');
			var calObj = $('#'+cal_id);

			process_events_list(cal_id,'initial');

			// hide the events list
			events_list_display(calObj);
			
		});	
		
	// MONTH JUMPER
		$('.ajde_evcal_calendar.eventmap').on('click','.evo_j_container a',function(){
			var container = $(this).closest('.evo_j_container');
			if(container.attr('data-m')!==undefined && container.attr('data-y')!==undefined){ // check month and day present on jumper
				var calid = container.closest('.ajde_evcal_calendar').attr('id');
				run_redo_map_upon_AJAX(calid);
			}
		});

	// MONTH SWITCHING
		$('.eventmap').on('click', '.evcal_arrows', function(){			
			var this_cal_id = $(this).closest('.eventmap').attr('id');					
			run_redo_map_upon_AJAX(this_cal_id);
		});

	// SORT and FIltering
		$('.eventon_filter_dropdown').on('click','p',function(){
			var this_cal_id = $(this).closest('.eventmap').attr('id');
			run_redo_map_upon_AJAX(this_cal_id);
		});

	// PROCESS event map
	
		// Process Event List for map markers
			function process_events_list(cal_id, type){
				var calendar = $('#'+cal_id),
					mapELEM = calendar.find('.evoGEO_map'),
					locationLIST = calendar.find('.evoGEO_locations');

				// Initial run
					if(type=='initial'){
						var evo_data = calendar.find('.evo-data'),
							mapscroll = evo_data.attr('data-mapscroll'),
							mapformat = evo_data.attr('data-mapformat');

						ibTXT = locationLIST.data('txt'); // text for info box

						//run gmap
						initializer(mapELEM.attr('id'), mapformat, mapscroll);
					}

				var events = calendar.find('.eventon_events_list').children('.eventon_list_event.event');

				if(events.length>0){
					mapELEM.parent().addClass('loading');

					var marker_url = $('#'+cal_id).find('.evoGEO_locations').attr('data-markerurl'),
						locationLIST = calendar.find('.evoGEO_locations');

					// clear saved values
						langlong = []; 
						var NEWLOCATIONS_ = {}, NEWLOCATIONS = {}; // arrays
						clearDebug(mapELEM);

					hideNoEventMSG(mapELEM); // hide no events message if visible
					events_list_display(calendar); // hide or show the events list

					count = 0;
					// foreach Event
						events.each(function(){
							var obj = $(this),
								evoInfo = obj.find('.evo_info'),
								eventidarray = [];

							// if event have location information
							if( evoInfo.attr('data-location_status')!='true') return true;

							var location_type = evoInfo.attr('data-location_type');

							coordinates = evoInfo.attr('data-latlng');
							if(coordinates === 'undefined' || coordinates == undefined) return true; // skip no latlng events

							locationDataStr = encodeURIComponent(coordinates);
							address = evoInfo.attr('data-location_address');

							var location_name = evoInfo.attr('data-location_name');
							var eventid = obj.attr('data-event_id');

							eventidarray.push(eventid);

							//if location exists in array
							if( locationDataStr in NEWLOCATIONS_){
								NEWLOCATIONS_[locationDataStr].events.push(eventid);
							}else{
								NEWLOCATIONS_[locationDataStr] = {
									'events':[eventid], 
									'coordinates':coordinates, 
									'name':location_name, 
									'address':address
								};
								count++;
							}

						});// end each

					// redo the locations array with clean keys
					x = 0;
					for( var key in NEWLOCATIONS_ ){
						var obj = NEWLOCATIONS_[key];
						NEWLOCATIONS[x] = obj;
						x++;
					}

					// if there are event locations
					if(count > 0){						
						if(type !='initial') clearMapMarkers();	
						ADDMARKER(address_pos, cal_id, marker_url, NEWLOCATIONS, count, mapELEM);
					}else{
						if(type !='initial') clearMapMarkers();
						setMaptoDefault(mapELEM);	
					}	

				}else{ // there are no events in the current location
					
					if(type !='initial'){
						clearMapMarkers();
						setMaptoDefault(mapELEM);
						events_list_display(calendar);
					}
					showNoEventMSG(mapELEM);
				}
			}
		
		// add map marker to the map LOOP
			function ADDMARKER(pos, cal_id, marker_url, LOCATIONSARRAY, length, mapELEM){

				THISLOCATION = LOCATIONSARRAY[pos];

				if(length < pos) return false;
				if( THISLOCATION === 'undefined' || THISLOCATION == undefined) return false;

				COORDS = (decodeURIComponent(THISLOCATION.coordinates));

				thisLATLON = '';

				langlong_ = COORDS.split(",");
				thisLATLON = new google.maps.LatLng( langlong_[0], langlong_[1] );

				if(thisLATLON === undefined || thisLATLON == '') return; 	

				//langlong[pos] = thisLATLON;
					
				if(length ==1) map.setCenter(thisLATLON); 		

				var event_count = THISLOCATION.events.length;
				var eventids = THISLOCATION.events;
				var event_count = (THISLOCATION.ri)? THISLOCATION.ri + event_count: event_count;

				// adding marker
					if(marker_url=='def'){ // default marker
						var marker = new google.maps.Marker({
							position: thisLATLON,
							map: map,
							zoom:14,
						});
					}else{
						var iconBase = decodeURIComponent(marker_url)+'/image.php?number='+event_count+'&url='+marker_url;
						//console.log(iconBase);
						var marker = new google.maps.Marker({
							position: thisLATLON,
							map: map,
							zoom:14,
							icon: iconBase
						});
					}

				MAPmarkers.push(marker);
								
				bounds.extend(thisLATLON);
					
				// Info window Stuff
					location_nameX = (THISLOCATION.name)? '<p>'+THISLOCATION.name+'</p>':'';	
					locationOBJ = $('#'+cal_id).find('.evoGEO_locations');					
					showloclink = locationOBJ.attr('data-loclink');

					// if there is link for location page
					if(showloclink == 'yes'){
						var locationSLUG = THISLOCATION.name.toLowerCase();
						locationSLUG = locationSLUG.replace(' ','-');
						
						locationURL = locationOBJ.attr('data-locurl');
						locationURL = locationURL+'/event-location/'+locationSLUG;

						location_nameX = '<p><a href="'+locationURL+'">'+THISLOCATION.name+'</a></p>';
					}			

					var ibtxt = ibTXT;

					var infobox_content = "<div class='evoIW'><div class='evoIWl'><p>"+event_count+"</p><span>"+ibtxt +"</span></div><div class='evoIWr'>"+location_nameX+THISLOCATION.address+'</div><div class="clear"></div></div>';
											
					// info window listener
						google.maps.event.addListener(marker, 'click', function() {
							
							if (!infowindow) {
								infowindow = new google.maps.InfoWindow();
							}
							infowindow.setContent(infobox_content);
							infowindow.open(map, marker);
							
							show_event(eventids, cal_id);
						});

						google.maps.event.addListener(marker, 'mouseover', function() {
							if (!infowindow) {
								infowindow = new google.maps.InfoWindow();
							}
							infowindow.setContent(infobox_content);
							infowindow.open(map, marker);
															
						});

						google.maps.event.addListener(infowindow,'closeclick',function(){
						   show_all_events(cal_id);
						}); 

				address_pos++;
				
				// if there are more markers to be added
					if (address_pos < length) {
						setTimeout(function() { ADDMARKER(address_pos, cal_id, marker_url, LOCATIONSARRAY, length, mapELEM); }, (timeout));
					}

				// last one - create map clusters
					if(address_pos == length && MAPmarkers.length > 0){
						if(mapELEM.data('clusters')!='yes')
							markerCluster.addMarkers(MAPmarkers);

						// remove loading animation
						mapELEM.parent().removeClass('loading');
					}

				map.fitBounds(bounds);	
			}

		// re-build the event map with markers
			function run_redo_map_upon_AJAX(calid){
				// hide new events list on months
				if( $('#'+calid).hasClass('eventmap')){
					$( document ).ajaxComplete(function(event, xhr, settings) {
						
						var data = settings.data;
						if( data.indexOf('action=the_ajax_hook') != -1){						
							//calObj.find('.eventon_list_event').hide();				
							
							process_events_list(calid,'redo');
							$('.eventmap').off('click', '.evcal_arrows');
						}
					});
				}
			}
	
		// remove markers
			function clearMapMarkers(){
				markerCluster.clearMarkers();

				for(var i =0; i< MAPmarkers.length; i++){
					MAPmarkers[i].setMap(null);
				}
				MAPmarkers = [];
				MAPmarkers.length = 0;
				address_pos = 0;

				bounds = new google.maps.LatLngBounds (); // declare new bounds for map
			}
			function setMaptoDefault(mapELEM){

				mapData = mapELEM.siblings('.evoGEO_locations');
				dlat = mapData.attr('data-dlat');
				dlon = mapData.attr('data-dlon');

				bounds = new google.maps.LatLngBounds ();
				newlatlng = new google.maps.LatLng(dlat, dlon);
				bounds.extend(newlatlng);
				map.setCenter(newlatlng);
				map.fitBounds(bounds);
				map.setZoom(14);
			}
		// display no event message
			function showNoEventMSG(mapELEM){
				mapELEM.parent().find('.evomap_noloc').fadeIn();
			}
			function hideNoEventMSG(mapELEM){
				mapELEM.parent().find('.evomap_noloc').fadeOut();
			}

		// debug record status
			function recordDebug(mapELEM, elm){
				var debug = mapELEM.siblings('.evomap_debug'),
					debugtext = debug.html();

				debug.html( debugtext +' '+elm);
			}
			function clearDebug(mapELEM){
				mapELEM.siblings('.evomap_debug').html('');
			}
		
		// re-fit markers into map
			$('body').on('click','.evo-mapfocus',function(){
				map.fitBounds(bounds);
			});
			
		// Show events for a location marker
			function show_event(eventsARRAY, cal_id){
				var calendar =$('#'+cal_id);
				calendar.find('.eventon_events_list').slideUp(function(){
					
					eventList = $('#'+cal_id).find('.evoEM_list');
					eventList.hide();
					calendar.find('.eventon_list_event').hide();

					// open as lightbox events
					if(eventList.attr('data-lightbox')=='yes'){
						append_popup_codes();
						eventslist = calendar.find('.eventon_events_list').html();
						$('body').find('.evoEM_pop_body').html(eventslist);

						popbody = $('body').find('.evoEM_pop_body');

						for(i=0; i< eventsARRAY.length; i++){
							popbody.find('.eventon_list_event[data-event_id='+eventsARRAY[i]+']').show();
						}

						$('body').find('.evoem_lightbox').fadeIn();
					}else{
					// none lightbox approach						
						for(i=0; i< eventsARRAY.length; i++){
							$('#'+cal_id).find('.eventon_list_event[data-event_id='+eventsARRAY[i]+']').show();
						}					
						$(this).delay(400).show();
						eventList.slideDown('slow');
					}					
				});
			}
			function show_all_events(cal_id){
				var calendar =$('#'+cal_id);
				calendar.find('.eventon_events_list').slideUp(function(){

					$(this).hide();
					//calendar.find('.eventon_list_event').show();
				
					//$(this).delay(400).slideDown('slow');
				});
			}

		// hide or show events list
			function events_list_display(cal){
				eventlist = cal.find('.evoEM_list');
				if(eventlist.attr('data-showe')=='yes'){
					eventlist.show();
				}else{
					eventlist.hide();
				}
			}

		// inpage lightbox events
			function append_popup_codes(){
				var popupcode = "<div class='evoem_lightbox' style='display:none'>";
						popupcode += "<div class='evoem_content_in'>";
							popupcode += "<div class='evoem_content_inin'>";
								popupcode += "<div class='evoem_lightbox_content'>";
									popupcode += "<a class='evopopclose'>X</a>";
									popupcode += "<div class='evoEM_pop_body evo_pop_body eventon_events_list evcal_eventcard'></div>";
								popupcode += "</div>";
							popupcode += "</div>";
						popupcode += "</div>";
					popupcode += "</div>";

				$('body').append(popupcode).addClass('evoem_overflow');
			}
			// close lightbox
				$('body').on('click','.evopopclose',function(){
					$(this).closest('.evoem_lightbox').fadeOut(function(){
						$('body').find('.evoem_lightbox').remove();
					});
					$('body').removeClass('evoem_overflow');
				});	
		
		// SUPPORT
			function stringCount(haystack) {
			    if (!haystack) {
			        return false;
			    }
			    else {
			        var words = haystack.split(','),
			            count = 1;

			        words.pop();

			        for (var i = 0, len = words.length; i < len; i++) {
			            count = parseInt(count) + 1;
			            //console.log(count);
			        }		        
			        return count;
			    }
			}
	
	
});