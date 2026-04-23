/**
 * @package Xpert Slider
 * @version 1.1
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

jQuery.noConflict();
jQuery(document).ready(function(fn){

    // Apply jquery UI Radio element style
    // Turn radios into btn-group
    fn('#jform_showtitle').addClass('btn-group');

    fn('.radio.btn-group label').addClass('btn');
    fn(".btn-group label:not(.active)").click(function() {
        var label = fn(this);
        var input = fn('#' + label.attr('for'));

        if (!input.prop('checked')) {
            label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
            if(input.val()== '') {
                    label.addClass('active btn-primary');
             } else if(input.val()==0) {
                    label.addClass('active btn-danger');
             } else {
            label.addClass('active btn-success');
             }
            input.prop('checked', true);
        }
    });

    fn(".btn-group input[checked=checked]").each(function() {
        if(fn(this).val()== '') {
           fn("label[for=" + fn(this).attr('id') + "]").addClass('active btn-primary');
        } else if(fn(this).val()==0) {
           fn("label[for=" + fn(this).attr('id') + "]").addClass('active btn-danger');
        } else {
            fn("label[for=" + fn(this).attr('id') + "]").addClass('active btn-success');
        }
    });

    //Bootsrap button on Joomla2.5 toolbar button
    fn('#toolbar li a').addClass('btn');

    // Bootstrap button for position
    var pos = fn('#jform_position-lbl').closest('li');
    pos.addClass('input-append').find('a').addClass('btn');

    // Boostraped alert message
    fn('#system-message ul li').addClass('alert alert-info');

    //Chosen Multiple selector
    fn(".chzn-select").chosen();

    // For module selecting box, increase the panelform height
    fn('#MODULE-options').next().find('.panelform').css('min-height', 150);

    fn('.cs-list a').popover({
        placement : 'right'
    });

    //k2 cat filter selection
    fn('#jform_params_k2_catfilter0').click(function(){
        fn('#jformparamsk2_catid').closest('li').hide();
        fn('#jformparamsk2_catid').closest('div.control-group').hide();
    });

    fn('#jform_params_k2_catfilter1').click(function(){
        fn('#jformparamsk2_catid').closest('li').show();
        fn('#jformparamsk2_catid').closest('div.control-group').show();
    });

    if (fn('#jform_params_k2_catfilter0').attr('checked')) {
        fn('#jformparamsk2_catid').closest('li').hide();
        fn('#jformparamsk2_catid').closest('div.control-group').hide();
    }

    if (fn('#jform_params_k2_catfilter1').attr('checked')) {
        fn('#jformparamsk2_catid').closest('li').show();
        fn('#jformparamsk2_catid').closest('div.control-group').show();
    }

    //Joomla cat filter selection
    fn('#jform_params_jomcatfilter0').click(function(){
        fn('#jform_params_jom_catid').closest('li').hide();
        fn('#jform_params_jom_catid').closest('div.control-group').hide();
    });

    fn('#jform_params_jomcatfilter1').click(function(){
        fn('#jform_params_jom_catid').closest('li').show();
        fn('#jform_params_jom_catid').closest('div.control-group').show();
    });

    if (fn('#jform_params_jomcatfilter0').attr('checked')) {
        fn('#jform_params_jom_catid').closest('li').hide();
        fn('#jform_params_jom_catid').closest('div.control-group').hide();
    }

    if (fn('#jform_params_jomcatfilter1').attr('checked')) {
        fn('#jform_params_jom_catid').closest('li').show();
        fn('#jform_params_jom_catid').closest('div.control-group').show();
    }

    //EasyBlog cat filter selection
    fn('#jform_params_ezb_catfilter0').click(function(){
        fn('#jformparamsezb_catid').closest('li').hide();
        fn('#jform_params_ezb_catid').closest('div.control-group').hide();
    });

    fn('#jform_params_ezb_catfilter1').click(function(){
        fn('#jformparamsezb_catid').closest('li').show();
        fn('#jform_params_ezb_catid').closest('div.control-group').show();
    });

    if (fn('#jform_params_ezb_catfilter0').attr('checked')) {
        fn('#jformparamsezb_catid').closest('li').hide();
        fn('#jform_params_ezb_catid').closest('div.control-group').hide();
    }

    if (fn('#jform_params_ezb_catfilter1').attr('checked')) {
        fn('#jformparamsezb_catid').closest('li').show();
        fn('#jform_params_ezb_catid').closest('div.control-group').show();
    }

    //Loader selection
    fn('#jform_params_loader0').click( function(){
        fn('#jform_params_pie_position').closest('li').show();
            fn('#jform_params_pie_position').closest('div.control-group').show();
        fn('#jform_params_bar_position').closest('li').hide();
            fn('#jform_params_bar_position').closest('div.control-group').hide();
    });
    if (fn('#jform_params_loader0').attr('checked')) {
        fn('#jform_params_pie_position').closest('li').show();
            fn('#jform_params_pie_position').closest('div.control-group').show();
        fn('#jform_params_bar_position').closest('li').hide();
            fn('#jform_params_bar_position').closest('div.control-group').hide();
    }

    fn('#jform_params_loader1').click( function(){
        fn('#jform_params_bar_position').closest('li').show();
            fn('#jform_params_bar_position').closest('div.control-group').show();
        fn('#jform_params_pie_position').closest('li').hide();
            fn('#jform_params_pie_position').closest('div.control-group').hide();
    });
    if (fn('#jform_params_loader1').attr('checked')) {
        fn('#jform_params_bar_position').closest('li').show();
            fn('#jform_params_bar_position').closest('div.control-group').show();
        fn('#jform_params_pie_position').closest('li').hide();
            fn('#jform_params_pie_position').closest('div.control-group').hide();
    }

    fn('#jform_params_loader2').click( function(){
        fn('#jform_params_bar_position').closest('li').hide();
            fn('#jform_params_bar_position').closest('div.control-group').hide();
        fn('#jform_params_pie_position').closest('li').hide();
            fn('#jform_params_pie_position').closest('div.control-group').hide();
    });
    if (fn('#jform_params_loader2').attr('checked')) {
        fn('#jform_params_bar_position').closest('li').hide();
            fn('#jform_params_bar_position').closest('div.control-group').hide();
        fn('#jform_params_pie_position').closest('li').hide();
            fn('#jform_params_pie_position').closest('div.control-group').hide();
    }

    //switch panel based on provided content source
    function panelSwitcher(text)
    {
        switch (text){
            case 'joomla': showJoomla(); break;
            case 'k2' : showK2(); break;
            case 'easyblog' : showEasyblog(); break;
            case 'flickr' : showFlickr(); break;
            case 'youtube' : showYoutube(); break;
            case 'module' : showModule(); break;
        }
    }
    //Show only selected panel and hide others
    function showJoomla()
    {
        fn('#JOOMLA-options').closest('div.panel').show();
        fn('#K2-options').closest('div.panel').hide();
        fn('#EASYBLOG-options').closest('div.panel').hide();
        fn('#FLICKR-options').closest('div.panel').hide();
        fn('#YOUTUBE-options').closest('div.panel').hide();
        fn('#MODULE-options').closest('div.panel').hide();

        //j3 fix
        fn('a[href="#options-JOOMLA"]').closest('li').show();
        fn('a[href="#options-K2"]').closest('li').hide();
        fn('a[href="#options-EASYBLOG"]').closest('li').hide();
        fn('a[href="#options-FLICKR"]').closest('li').hide();
        fn('a[href="#options-YOUTUBE"]').closest('li').hide();
        fn('a[href="#options-MODULE"]').closest('li').hide();

        // Advance module manager tab layout fix
        fn('.tab-JOOMLA').show();
        fn('.tab-K2').hide();
        fn('.tab-EASYBLOG').hide();
        fn('.tab-FLICKR').hide();
        fn('.tab-YOUTUBE').hide();
        fn('.tab-MODULE').hide();
    }
    function showK2()
    {
        fn('#JOOMLA-options').closest('div.panel').hide();
        fn('#K2-options').closest('div.panel').show();
        fn('#EASYBLOG-options').closest('div.panel').hide();
        fn('#FLICKR-options').closest('div.panel').hide();
        fn('#YOUTUBE-options').closest('div.panel').hide();
        fn('#MODULE-options').closest('div.panel').hide();

        //j3 fix
        fn('a[href="#options-JOOMLA"]').closest('li').hide();
        fn('a[href="#options-K2"]').closest('li').show();
        fn('a[href="#options-EASYBLOG"]').closest('li').hide();
        fn('a[href="#options-FLICKR"]').closest('li').hide();
        fn('a[href="#options-YOUTUBE"]').closest('li').hide();
        fn('a[href="#options-MODULE"]').closest('li').hide();

        // Advance module manager tab layout fix
        fn('.tab-JOOMLA').hide();
        fn('.tab-K2').show();
        fn('.tab-EASYBLOG').hide();
        fn('.tab-FLICKR').hide();
        fn('.tab-YOUTUBE').hide();
        fn('.tab-MODULE').hide();
    }
    function showEasyblog()
    {
        fn('#JOOMLA-options').closest('div.panel').hide();
        fn('#K2-options').closest('div.panel').hide();
        fn('#EASYBLOG-options').closest('div.panel').show();
        fn('#FLICKR-options').closest('div.panel').hide();
        fn('#YOUTUBE-options').closest('div.panel').hide();
        fn('#MODULE-options').closest('div.panel').hide();

        //j3 fix
        fn('a[href="#options-JOOMLA"]').closest('li').hide();
        fn('a[href="#options-K2"]').closest('li').hide();
        fn('a[href="#options-EASYBLOG"]').closest('li').show();
        fn('a[href="#options-FLICKR"]').closest('li').hide();
        fn('a[href="#options-YOUTUBE"]').closest('li').hide();
        fn('a[href="#options-MODULE"]').closest('li').hide();

        // Advance module manager tab layout fix
        fn('.tab-JOOMLA').hide();
        fn('.tab-K2').hide();
        fn('.tab-EASYBLOG').show();
        fn('.tab-FLICKR').hide();
        fn('.tab-YOUTUBE').hide();
        fn('.tab-MODULE').hide();
    }
    function showFlickr()
    {
        fn('#JOOMLA-options').closest('div.panel').hide();
        fn('#K2-options').closest('div.panel').hide();
        fn('#EASYBLOG-options').closest('div.panel').hide();
        fn('#FLICKR-options').closest('div.panel').show();
        fn('#YOUTUBE-options').closest('div.panel').hide();
        fn('#MODULE-options').closest('div.panel').hide();

        //j3 fix
        fn('a[href="#options-JOOMLA"]').closest('li').hide();
        fn('a[href="#options-K2"]').closest('li').hide();
        fn('a[href="#options-EASYBLOG"]').closest('li').hide();
        fn('a[href="#options-FLICKR"]').closest('li').show();
        fn('a[href="#options-YOUTUBE"]').closest('li').hide();
        fn('a[href="#options-MODULE"]').closest('li').hide();

        // Advance module manager tab layout fix
        fn('.tab-JOOMLA').hide();
        fn('.tab-K2').hide();
        fn('.tab-EASYBLOG').hide();
        fn('.tab-FLICKR').show();
        fn('.tab-YOUTUBE').hide();
        fn('.tab-MODULE').hide();
    }
    function showYoutube()
    {
        fn('#JOOMLA-options').closest('div.panel').hide();
        fn('#K2-options').closest('div.panel').hide();
        fn('#EASYBLOG-options').closest('div.panel').hide();
        fn('#FLICKR-options').closest('div.panel').hide();
        fn('#MODULE-options').closest('div.panel').hide();
        fn('#YOUTUBE-options').closest('div.panel').show();

        //j3 fix
        fn('a[href="#options-JOOMLA"]').closest('li').hide();
        fn('a[href="#options-K2"]').closest('li').hide();
        fn('a[href="#options-EASYBLOG"]').closest('li').hide();
        fn('a[href="#options-FLICKR"]').closest('li').hide();
        fn('a[href="#options-MODULE"]').closest('li').hide();
        fn('a[href="#options-YOUTUBE"]').closest('li').show();

        // Advance module manager tab layout fix
        fn('.tab-JOOMLA').hide();
        fn('.tab-K2').hide();
        fn('.tab-EASYBLOG').hide();
        fn('.tab-FLICKR').hide();
        fn('.tab-MODULE').hide();
        fn('.tab-YOUTUBE').show();
    }

     //Show only selected panel and hide others
    function showModule()
    {
        fn('#JOOMLA-options').closest('div.panel').hide();
        fn('#K2-options').closest('div.panel').hide();
        fn('#EASYBLOG-options').closest('div.panel').hide();
        fn('#FLICKR-options').closest('div.panel').hide();
        fn('#YOUTUBE-options').closest('div.panel').hide();
        fn('#MODULE-options').closest('div.panel').show();

        //j3 fix
        fn('a[href="#options-JOOMLA"]').closest('li').hide();
        fn('a[href="#options-K2"]').closest('li').hide();
        fn('a[href="#options-EASYBLOG"]').closest('li').hide();
        fn('a[href="#options-FLICKR"]').closest('li').hide();
        fn('a[href="#options-YOUTUBE"]').closest('li').hide();
        fn('a[href="#options-MODULE"]').closest('li').show();

        // Advance module manager tab layout fix
        fn('.tab-JOOMLA').hide();
        fn('.tab-K2').hide();
        fn('.tab-EASYBLOG').hide();
        fn('.tab-FLICKR').hide();
        fn('.tab-YOUTUBE').hide();
        fn('.tab-MODULE').show();
    }

    // Content source select and push to joomla input field related to it
    // and hide modal
    fn('.cs-list a').on('click',function(){
        var el = fn(this),
            text = el.find('span').text();
        if(el.hasClass('notavailable')) {
            return false;
        }

        fn('#jform_params_content_source').attr('value',text);
        fn('#content-source').bsmodal('hide');

        //replace button with selected source
        fn('.cs-btn').removeClass('joomla k2 easyblog flickr youtube module').addClass(text);
        fn('.cs-btn span').text(text);

        panelSwitcher(text);

        return false;
    });

    //set content soruce
    var cs = fn('#jform_params_content_source').attr('value');
    fn('.cs-btn').addClass(cs);
    fn('.cs-btn span').text(cs);
    panelSwitcher(cs);

    //remove label li and push it to previous element
    fn('div.remove-lbl').each(function(){
       var content = fn(this);
        //push it to previous li
        fn(this).closest('li')
            .prev()
            .append(content);
        //remove paren li
        fn(this).closest('li').next().remove();
    });
});