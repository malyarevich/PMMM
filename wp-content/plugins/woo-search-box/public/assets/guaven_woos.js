specials = ["'",'ä', 'ö', 'ü', 'à', 'â', 'é', 'è', 'ê', 'ë', 'ï', 'î', 'ô', 'ù', 'û', 'ÿ', 'å', 'ó', 'ú', 'ů', 'ý', 'ž',
    'á', 'č', 'ď', 'ě', 'í', 'ň', 'ř', 'š', 'ť', 'ñ', 'ç', 'ğ',
    'ı', 'İ', 'ş', 'ã', 'õ', 'ά', 'έ', 'ή', 'ί', 'ϊ', 'ΐ', 'ό', 'ύ', 'ϋ', 'ΰ', 'ώ', 'ə',
    'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', //russian cyrillic
    'љ', 'њ', 'ѓ', 'ќ', 'џ', //macedonian special letters
];
specials_replacers = ['&#8217;','a', 'o', 'u', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'o', 'u', 'u', 'y', 'a', 'o', 'u', 'u', 'y', 'z',
    'a', 'c', 'd', 'e', 'i', 'n', 'r', 's', 't', 'n', 'c', 'g',
    'i', 'i', 's', 'a', 'o', 'α', 'ε', 'η', 'ι', 'ι', 'ι', 'ο', 'υ', 'υ', 'υ', 'ω', 'e',
    'a', 'b', 'v', 'g', 'd', 'e', 'io', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'c', 'sh', 'sht', 'a', 'i', 'y', 'e', 'yu', 'ya',
    'lj', 'nj', 'g', 'k', 'dz'
];
ilkherf = '';
prids_object = "";

jQuery(window).load(function() {
    //jQuery(function () {

    if (guaven_woos_dttrr == 1 && typeof(Storage) !== "undefined") {
        if ((localStorage.keywordsuccess == undefined)) localStorage.setItem("keywordsuccess", "");
        if ((localStorage.keywordfailed == undefined)) localStorage.setItem("keywordfailed", "");
        if ((localStorage.keywordcorrected == undefined)) localStorage.setItem("keywordcorrected", "");
        if ((localStorage.unid == undefined)) localStorage.setItem("unid", "user_" + guaven_woos_uniqid());
        setTimeout(function() {
            guaven_woos_send_tr_data();
        }, 8000);
    }

    guaven_woos_cache_keywords_arr = new Array();
    guaven_woos_cache_cat_keywords_arr = new Array();

    if (guaven_woos_large_data == 1) {
        for (var guaven_woos_key in guaven_woos_cache_keywords) {
            var indexA = guaven_woos_cache_keywords[guaven_woos_key].substring(0, 1).toLowerCase();

            if (!guaven_woos_cache_keywords_arr.hasOwnProperty(indexA)) {
                guaven_woos_cache_keywords_arr[indexA] = new Array();
            }
            guaven_woos_cache_keywords_arr[indexA][guaven_woos_key] = guaven_woos_cache_keywords[guaven_woos_key];

        }

        for (var guaven_woos_key in guaven_woos_category_keywords) {
            var indexA = guaven_woos_category_keywords[guaven_woos_key].substring(0, 1).toLowerCase();

            if (!guaven_woos_cache_cat_keywords_arr.hasOwnProperty(indexA)) {
                guaven_woos_cache_cat_keywords_arr[indexA] = new Array();
            }
            guaven_woos_cache_cat_keywords_arr[indexA][guaven_woos_key] = guaven_woos_category_keywords[guaven_woos_key];

        }

    }


    guaven_woos_cache_keywords_spec = new Array();
    for (var guaven_woos_key in guaven_woos_cache_keywords) {
        guaven_woos_cache_keywords_spec[guaven_woos_key] = guaven_woos_replace_array(guaven_woos_cache_keywords[guaven_woos_key].toLowerCase(), specials, specials_replacers);
    }

    jQuery('[name="s"]').on('focus', function() {
        jQuery(".guaven_woos_suggestion").css('display', 'block');
        var guaven_woos_input = jQuery(this);
        var guaven_woos_offset = guaven_woos_input.offset();
        guaven_woos_input.attr('autocomplete', 'off');
        jQuery(".guaven_woos_suggestion").css('left', guaven_woos_offset.left);
        jQuery(".guaven_woos_suggestion").css('top', guaven_woos_offset.top + parseFloat(guaven_woos_input.outerHeight()));
        jQuery(".guaven_woos_suggestion").outerWidth(parseFloat(guaven_woos_input.outerWidth()) * guaven_woos_sugbarwidth);

        if (jQuery(this).val() != '') {
            jQuery(this).trigger("keyup");
        } else if (focused == 0) {
            jQuery('.guaven_woos_suggestion').html("<ul><li>" + guaven_woos_showinit + "</li></ul>");

            if (guaven_woos_pinnedtitle && guaven_woos_pinned_html) {
                guaven_woos_pinned_final = '';
                for (var guaven_woos_ph in guaven_woos_pinned_html) {
                    guaven_woos_pinned_final += guaven_woos_format(guaven_woos_pinned_html[guaven_woos_ph], guaven_woos_pinned_keywords[guaven_woos_ph]);
                }
                jQuery('.guaven_woos_suggestion').append("<p class=\"guaven_woos_pinnedtitle\">" + guaven_woos_pinnedtitle + "</p><ul class='guaven_woos_suggestion_unlisted'>" +
                    guaven_woos_pinned_final + "</ul>");
            }

            if (guaven_woos_persprod != '' && guaven_woos_persprod != undefined) {
                jQuery('.guaven_woos_suggestion').append("<p class=\"guaven_woos_pinnedtitle\">" + guaven_woos_perst +
                    "</p><ul class='guaven_woos_suggestion_unlisted'>" + guaven_woos_format(guaven_woos_persprod) + "</ul>");

            }

        }
        focused = 1;
    });

    jQuery('[name="s"]').on('focusout', function() {
        focused = 0;
        setTimeout(function() {
            jQuery(".guaven_woos_suggestion").css('display', 'none');
        }, 500);
    });
    runSearch = '';
    jQuery('[name="s"]').on('keyup', function(e) {

      prids_object = "";

        if (e.which === 40 || e.which === 38)
            return;
        guaven_woos_finalresult = '';
        rescount = 0;
        guaven_woos_tempval = jQuery(this).val();


        clearTimeout(runSearch);
        runSearch = setTimeout(function() {

            if (guaven_woos_tempval.length >= (minkeycount - 1)) {

                guaven_woos_result_loop(0);

                if (rescount <= maxtypocount) {

                    maxpercent = 0;
                    finalpercent = 0;
                    maxsimilarword = '';
                    guaven_woos_result_loop(1);

                }


                guaven_woos_cfinalresult = '';
                if (guaven_woos_categories_enabled == 1) {
                    guaven_woos_cfinalresult = guaven_woos_result_catadd();
                    if (guaven_woos_cfinalresult != '')
                        guaven_woos_cfinalresult = "<ul class='guaven_woos_suggestion_catul'>" + guaven_woos_cfinalresult + "</ul>";
                }

              if (guaven_woos_backend == 1) {
                    if (prids_object > 4088) prids_object = prids_object.substrin(0, 4088);
                    document.cookie = "prids_object_cookie=" + prids_object;
                    document.cookie = "prids_keyword_cookie=" + guaven_woos_tempval;
                  //  console.log('done'+prids_object);
                }


                jQuery('.guaven_woos_suggestion').html(guaven_woos_cfinalresult + "<ul>" + guaven_woos_finalresult + "</ul>");
                if (rescount > 0)
                    jQuery(".guaven_woos_suggestion").css('display', 'block');
                else if (guaven_woos_shownotfound == '' && guaven_woos_cfinalresult == '')
                    jQuery(".guaven_woos_suggestion").css('display', 'none');
                if (guaven_woos_shownotfound != '' && guaven_woos_finalresult == '' && guaven_woos_cfinalresult == '') {
                    jQuery('.guaven_woos_suggestion').html("<ul><li>" + guaven_woos_shownotfound + "</li></ul>");
                    if (guaven_woos_dttrr == 1 && typeof(Storage) !== "undefined") {
                        localStorage.keywordfailed = localStorage.keywordfailed + guaven_woos_tempval + ', ';
                    }
                    if (guaven_woos_populars_enabled == 1 && guaven_woos_populars_html) {
                        guaven_woos_populars_final = '';
                        for (var guaven_woos_pps in guaven_woos_populars_html) {
                            guaven_woos_populars_final += guaven_woos_format(guaven_woos_populars_html[guaven_woos_pps], guaven_woos_populars_keywords[guaven_woos_pps]);
                        }
                        jQuery('.guaven_woos_suggestion').append("<ul class='guaven_woos_suggestion_unlisted guaven_woos_suggestion_populars'>" +
                            guaven_woos_populars_final + "</ul>");
                    }
                }
            } else if (guaven_woos_showinit != '') {
                jQuery('.guaven_woos_suggestion').html("<ul><li>" + guaven_woos_showinit + "</li></ul>");

            }
        }, guaven_engine_start_delay);
    });



    var li = jQuery('.guaven_woos_suggestion_unlisted>li');
    var liSelected;
    jQuery(window).keydown(function(e) {
        if (e.which === 40) {

            if (liSelected) {

                liSelected.removeClass('guaven_woos_selected');
                next = liSelected.next();
                if (next.length > 0) {
                    liSelected = next.addClass('guaven_woos_selected');

                } else {
                    liSelected = jQuery('.guaven_woos_suggestion>ul>li:first').addClass('guaven_woos_selected');

                }
            } else {
                liSelected = li.eq(0).addClass('guaven_woos_selected');
            }
        } else if (e.which === 38) {
            if (liSelected) {
                liSelected.removeClass('guaven_woos_selected');
                next = liSelected.prev();
                if (next.length > 0) {
                    liSelected = next.addClass('guaven_woos_selected');
                } else {
                    liSelected = li.last().addClass('guaven_woos_selected');
                }
            } else {
                liSelected = li.last().addClass('guaven_woos_selected');
            }
        }
    });

});



window.guaven_woos_getcookie = function(name) {
    match = document.cookie.match(new RegExp(name + '=([^;]+)'));
    if (match)
        return match[1];
}



function guaven_woos_levenshtein(s1, s2) {

    if (s1 == s2) {
        return 0;
    }

    var s1_len = s1.length;
    var s2_len = s2.length;
    if (s1_len === 0) {
        return s2_len;
    }
    if (s2_len === 0) {
        return s1_len;
    }

    // BEGIN STATIC
    var split = false;
    try {
        split = !('0')[0];
    } catch (e) {
        split = true; // Earlier IE may not support access by string index
    }
    // END STATIC
    if (split) {
        s1 = s1.split('');
        s2 = s2.split('');
    }

    var v0 = new Array(s1_len + 1);
    var v1 = new Array(s1_len + 1);

    var s1_idx = 0,
        s2_idx = 0,
        cost = 0;
    for (s1_idx = 0; s1_idx < s1_len + 1; s1_idx++) {
        v0[s1_idx] = s1_idx;
    }
    var char_s1 = '',
        char_s2 = '';
    for (s2_idx = 1; s2_idx <= s2_len; s2_idx++) {
        v1[0] = s2_idx;
        char_s2 = s2[s2_idx - 1];

        for (s1_idx = 0; s1_idx < s1_len; s1_idx++) {
            char_s1 = s1[s1_idx];
            cost = (char_s1 == char_s2) ? 0 : 1;
            var m_min = v0[s1_idx + 1] + 1;
            var b = v1[s1_idx] + 1;
            var c = v0[s1_idx] + cost;
            if (b < m_min) {
                m_min = b;
            }
            if (c < m_min) {
                m_min = c;
            }
            v1[s1_idx + 1] = m_min;
        }
        var v_tmp = v0;
        v0 = v1;
        v1 = v_tmp;
    }
    return v0[s1_len];
}

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

function guaven_woos_replace_array(replaceString, find, replace) {
    for (var i = 0; i < find.length; i++) {
        replaceString = replaceString.replaceAll(find[i], replace[i]);
    }
    return replaceString;
};


function guaven_woos_concatsearch(arrdata, str) {
    //  return -1;
    var hasil = 0;
    var respoint = 0;
    var arrdata_arr = arrdata.split(" ");
    for (i = 0; i < arrdata_arr.length; i++) {
        respoint = respoint + str.indexOf(arrdata_arr[i]);
        if (str.indexOf(arrdata_arr[i]) == -1) hasil = -1;
    }
    if (hasil == -1) respoint = -1;
    return respoint;
}

function guaven_woos_stripQuotes(s) {
    var t = s.length;
    if (s.charAt(0) == '"') s = s.substring(1, t--);
    if (s.charAt(--t) == '"') s = s.substring(0, t);
    return s;
}

function guaven_woos_format(str, ttl) {
    str = str.replaceAll('{{t}}', ttl);
    str = str.replaceAll('{{s}}', '</span> <span class=\"guaven_woos_hidden guaven_woos_hidden_tags\">');
    str = str.replaceAll('{{l}}', '<li class=\"guaven_woos_suggestion_list\" tabindex=');
    str = str.replaceAll('{{d}}', '\"><div class=\"guaven_woos_div\"><img class=\"guaven_woos_img\" src=\"');
    str = str.replaceAll('{{i}}', '\"></div><div class=\"guaven_woos_titlediv\">');
    str = str.replaceAll('{{e}}', '</div></a> </li>');
    str = str.replaceAll('{{u}}', guaven_woos_updir);
    return str;
}

function guaven_woos_result_push(guaven_woos_temphtml, guaven_woos_temptitle, woos_search_existense, guaven_woos_tempval,stortype = '') {
    rescount++;
    tempformatted = guaven_woos_format(guaven_woos_temphtml, guaven_woos_temptitle);
    var guaven_woos_temphtml_parsed = jQuery.parseHTML(tempformatted);


    if (guaven_woos_backend==1){
          prids_object = prids_object + guaven_woos_temphtml_parsed[1].id.replace("prli_", "") + ",";
    }
    if (stortype == '') {
        if (guaven_woos_dttrr == 1 && typeof(Storage) !== "undefined" && localStorage.keywordsuccess.indexOf(guaven_woos_tempval) == -1) {
            localStorage.keywordsuccess = localStorage.keywordsuccess + guaven_woos_tempval + ', ';
        }
    } else if (guaven_woos_dttrr == 1 && typeof(Storage) !== "undefined" && localStorage.keywordcorrected.indexOf(guaven_woos_tempval) == -1) {
        localStorage.keywordcorrected = localStorage.keywordcorrected + guaven_woos_tempval + ', ';
    }
    return woos_keyformat(woos_search_existense) + '~g~v~n~' + tempformatted;
}


function guaven_woos_result_loop(tries) {
    var keyhelper = new Array();
    ilkherf = guaven_woos_tempval.toLowerCase().substring(0, 1);
    if (guaven_woos_tempval.toLowerCase().indexOf('guaven') > -1) return;
    if (guaven_woos_large_data == 1) {
        guaven_woos_findin_data = guaven_woos_cache_keywords_arr[ilkherf];
    } else {
        guaven_woos_findin_data = guaven_woos_cache_keywords;
    }

    guaven_woos_tempval_spec = guaven_woos_replace_array(guaven_woos_tempval.toLowerCase(), specials, specials_replacers);

    for (var guaven_woos_key in guaven_woos_findin_data) {
        var guaven_woos_temptitle = guaven_woos_cache_keywords[guaven_woos_key];
        var guaven_woos_temphtml = guaven_woos_cache_html[guaven_woos_key];
        guaven_woos_temptitle_spec = guaven_woos_cache_keywords_spec[guaven_woos_key];
        if (guaven_woos_exactmatch == 1) {
            guaven_woos_temptitle_exact_string = guaven_woos_temptitle_spec.replace(/(<([^>]+)>)/ig, ""); // Returns: bar
            guaven_woos_temptitle_exact_string = guaven_woos_temptitle_exact_string.replaceAll(",", " ");
            guaven_woos_temptitle_exact_string = guaven_woos_stripQuotes(guaven_woos_temptitle_exact_string).toLowerCase();
            guaven_woos_temptitle_exact = guaven_woos_temptitle_exact_string.split(" ");
            for (var exact_key in guaven_woos_temptitle_exact) {
                if (guaven_woos_temptitle_exact[exact_key] == guaven_woos_tempval.toLowerCase()) {
                    keyhelper.push(guaven_woos_result_push(guaven_woos_temphtml, guaven_woos_temptitle, exact_key,guaven_woos_tempval));
                }
            }

        } else if (tries == 0) {
            var woos_search_existense = guaven_woos_temptitle.toLowerCase().indexOf(guaven_woos_tempval.toLowerCase());
            if (woos_search_existense == -1) woos_search_existense = guaven_woos_temptitle_spec.indexOf(guaven_woos_tempval_spec);

            if (guaven_woos_temptitle.indexOf(guaven_woos_wpml) > -1 &&
                woos_search_existense > -1
            ) {
                keyhelper.push(guaven_woos_result_push(guaven_woos_temphtml, guaven_woos_temptitle, woos_search_existense,guaven_woos_tempval));
            }
        } else {

            var concatsearch = guaven_woos_concatsearch(guaven_woos_tempval.toLowerCase(), guaven_woos_temptitle.toLowerCase());
            if (concatsearch > -1) {
                keyhelper.push(guaven_woos_result_push(guaven_woos_temphtml, guaven_woos_temptitle, concatsearch + maxcount,guaven_woos_tempval));
            } else if (guaven_woos_correction_enabled == 1) {
                var lev_a = guaven_woos_tempval.toLowerCase();
                var lev_b = guaven_woos_temptitle.substring(0, lev_a.length).toLowerCase();

                var lev_a_spec = guaven_woos_tempval_spec;
                var lev_b_spec = guaven_woos_temptitle_spec.substring(0, lev_a.length);

                finalpercent = guaven_woos_levenshtein(lev_a_spec, lev_b_spec);
                finalpercent_spec = guaven_woos_levenshtein(lev_a_spec, lev_b_spec);

                if (guaven_woos_temptitle.indexOf(guaven_woos_wpml) > -1 && finalpercent <= 3 &&
                    finalpercent >= 1 && finalpercent <= (lev_a.length - 3)
                    &&
                    finalpercent_spec <= 3 && finalpercent_spec >= 1 && finalpercent_spec <= (lev_a.length - 3)) {
                    keyhelper.push(guaven_woos_result_push(guaven_woos_temphtml, guaven_woos_temptitle, (100 + maxcount + guaven_woos_temptitle.indexOf(guaven_woos_wpml)), guaven_woos_tempval,'corrected'));
                }

            }
        }
    }
    keyhelper.sort();
    var rescount_new = 0;
    for (var keyh in keyhelper) {
        if (rescount_new < maxcount) {
            purevalue = keyhelper[keyh].split("~g~v~n~");
            if (guaven_woos_finalresult.indexOf(purevalue[1]) == -1) {
                rescount_new++;
                guaven_woos_finalresult = guaven_woos_finalresult + purevalue[1];
            }

        }

    }

}

function woos_keyformat(numm) {
    if (numm < 10) numstr = '000' + numm;
    else if (numm < 100) numstr = '00' + numm;
    else if (numm < 1000) numstr = '0' + numm;
    return numstr;
}


function guaven_woos_result_catadd() {
    var crescount = 0;
    var guaven_woos_cfinalresult = '';

    ilkherf = guaven_woos_tempval.toLowerCase().substring(0, 1);

    if (guaven_woos_large_data == 1) {
        guaven_woos_findin_data_cat = guaven_woos_cache_cat_keywords_arr[ilkherf];
    } else {
        guaven_woos_findin_data_cat = guaven_woos_category_keywords;
    }

    for (var guaven_woos_ckey in guaven_woos_findin_data_cat) {

        var guaven_woos_ctemptitle = guaven_woos_category_keywords[guaven_woos_ckey];
        var guaven_woos_ctemphtml = guaven_woos_category_html[guaven_woos_ckey];


        guaven_woos_ctempval_spec = guaven_woos_replace_array(guaven_woos_tempval.toLowerCase(), specials, specials_replacers);
        guaven_woos_ctemptitle_spec = guaven_woos_replace_array(guaven_woos_ctemptitle.toLowerCase(), specials, specials_replacers);
        if (crescount < cmaxcount &&
            (guaven_woos_ctemptitle.toLowerCase().indexOf(guaven_woos_tempval.toLowerCase()) > -1 ||

                guaven_woos_ctemptitle_spec.toLowerCase().indexOf(guaven_woos_tempval_spec.toLowerCase()) > -1)) {
            crescount++;
            guaven_woos_cfinalresult = guaven_woos_cfinalresult + guaven_woos_format(guaven_woos_ctemphtml, '');
        }
    }
    return guaven_woos_cfinalresult;
}

function guaven_woos_send_tr_data() {
    guaven_woos_data.failed = localStorage.keywordfailed;
    guaven_woos_data.success = localStorage.keywordsuccess;
    guaven_woos_data.corrected = localStorage.keywordcorrected;
    guaven_woos_data.unid = localStorage.unid;
    jQuery.post(guaven_woos_ajaxurl, guaven_woos_data, function(response) {
        localStorage.keywordfailed = '';
        localStorage.keywordsuccess = '';
        localStorage.keywordcorrected = '';
    });
}

function guaven_woos_uniqid() {
    var ts = String(new Date().getTime()),
        i = 0,
        out = '';
    for (i = 0; i < ts.length; i += 2) {
        out += Number(ts.substr(i, 2)).toString(36);
    }
    return ('d' + out);
}
