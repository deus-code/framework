/**
 * @project DC Framework
 * @link https://deus-code.ru
 * @author Deus Code <root@deus-code.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

function DCFramework() {
    var self = this;
    this.block_ajax = false;
    this.popup = false;

    this.serializefiles = function(objForm) {
        /* ADD FILE TO PARAM AJAX */
        var formData = new FormData();
        $.each($(objForm).find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });
        var params = $(objForm).serializeArray();
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        return formData;
    };

    this.initAjaxLinks = function () {
        $('a.dcAjaxLink').each(function(){
            $(this).removeClass('dcAjaxLink');
            $(this).on('click',function(){
                var link = $(this).attr('href');
                $.ajax({
                    type: "GET",
                    url: link,
                    dataType : 'json',
                    processData: false,
                    contentType: false,
                    success: function(data){
                        if(data.callback){
                            eval(data.callback)();
                        }
                    }
                });
                return false;
            });
        });
    };

    this.initPopupBtn = function () {
        $('a.dcPopupBtn').each(function(){
            $(this).removeClass('dcPopupBtn');
            $(this).on('click',function(){
                var src = $(this).data('src');
                $.ajax({
                    type: "POST",
                    url: src,
                    dataType : 'json',
                    processData: false,
                    contentType: false,
                    success: function(data){
                        if(typeof data.popup != "undefined") self.showPopup(data.popup);
                    }
                });
                return false;
            });
        });
    };

    this.initAjaxForms = function () {
        $('form.dcAjaxForm').each(function(index){
            var form = $(this);
            var container;
            var preloader = $('<div/>').addClass('dcPreloader').appendTo(form);
            if(form.hasClass('dcPopupForm')){
                container = $('.dcPopupContent');
            }else{
                container = form;
            }
            self.validateForm();
            form.removeClass('dcAjaxForm');
            form.on('submit',function(){
                if(form.valid() && self.block_ajax==false){
                    self.block_ajax = true;
                    preloader.show();
                    $.ajax({
                        type: "POST",
                        url: form.attr('action'),
                        data: self.serializefiles(form),
                        dataType : 'json',
                        processData: false,
                        contentType: false,
                        success: function(data){
                            preloader.hide();
                            self.block_ajax = false;
                            if(typeof data.form != "undefined"){
                                if(data.form.error){
                                    $('.dcErrorForm').remove();
                                    $('.dcSuccessForm').remove();
                                    $('<div/>').addClass('dcErrorForm').html(data.form.error).prependTo(container);
                                }
                                if(data.form.success){
                                    $('.dcErrorForm').remove();
                                    $('.dcSuccessForm').remove();
                                    $('<div/>').addClass('dcSuccessForm').html(data.form.success).prependTo(container);
                                    if(data.form.successCallback){
                                        eval(data.form.successCallback)();
                                    }
                                }
                            }
                        }
                    });
                }
                return false;
            });
        });
    };

    this.validateForm = function(){
        $.validator.addMethod("phoneFormat", function (value, element) {
            if($(element).attr('required')) {
                if (value=='+7 (___) ___-__-__'){
                    $(element).val('');
                    return false;
                }else{
                    return value.match(/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/);
                }
            }else{
                if(value.length>0){
                    if (value=='+7 (___) ___-__-__'){
                        $(element).val('');
                        return true;
                    }else{
                        return value.match(/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/);
                    }
                }else{
                    return true;
                }
            }
        }, "Введите корректный номер телефона");
        var validateForm = $('.dcAjaxForm:visible').not('.is_validate');
        validateForm.each(function() {
            $(this).addClass('is_validate').validate();
        });
        var phoneMask = $('input.dcPhoneMask:visible').not('.is_masked');
        phoneMask.each(function() {
            $(this).addClass('is_masked').rules('add', {
                phoneFormat: true
            });
        });
        phoneMask.mask("+7 (999) 999-99-99");
    };

    this.showPopup = function(options){
        var body = $('body');
        self.popup = $('<div/>').addClass('dcPopup');
        if(!options.btnOne && !options.btnTwo) self.popup.addClass('dcPopupNoBottom');
        var popupContainer;
        if(options.form) {
            var popupForm = $('<form/>').addClass('dcAjaxForm').addClass('dcPopupForm').appendTo(self.popup);
            if(options.formAction) popupForm.attr('action',options.formAction);
            popupContainer = popupForm;
        }else{
            popupContainer = self.popup;
        }

        var popupCloseBtn = $('<div/>').addClass('dcPopupCloseBtn').appendTo(popupContainer);
        var popupHeader = $('<div/>').addClass('dcPopupHeader').appendTo(popupContainer);
        var popupContentWrap = $('<div/>').addClass('dcPopupContentWrap').appendTo(popupContainer);
        var popupContent = $('<div/>').addClass('dcPopupContent').appendTo(popupContentWrap);
        var popupFooter = $('<div/>').addClass('dcPopupFooter').appendTo(popupContainer);
        var popupOverlay = $('<div/>').addClass('dcPopupOverlay').appendTo(body);
        self.popup.appendTo(body);

        if(options.class) {
            self.popup.addClass(options.class);
        }

        if(options.content) {
            popupContent.html(options.content);
        }else{
            popupContent.hide();
        }
        if(options.title) popupHeader.html('<h3>'+options.title+'</h3>');

        if(options.btnOne) {
            var btn = $('<input class="dcPopupFooterBtn dcPopupFooterBtnOne" value="'+options.btnOne+'" type="submit">');
            btn.appendTo(popupFooter);
        }
        if(options.btnTwo) {
            var btnTwo = $('<input class="dcPopupFooterBtn dcPopupFooterBtnTwo" value="'+options.btnTwo+'" type="button">');
            btnTwo.appendTo(popupFooter);
            btnTwo.off('click').on('click',self.closePopup);
            popupFooter.addClass('dcPopupFooterForTwoBtn');
        }
        if(options.hideClose) {
            popupCloseBtn.hide();
        }else{
            popupCloseBtn.show();
        }
        popupOverlay.show();
        self.popup.show();
        popupCloseBtn.off('click').on('click',self.closePopup);
        popupOverlay.off('click').on('click',self.closePopup);
        self.initAjaxForms();
        self.initAjaxLinks();
        self.initPopupBtn();
    };

    this.closePopup = function () {
        $('.dcPopupOverlay').remove();
        $('.dcPopup').remove();
    };

    this.init = function() {
        self.initAjaxForms();
        self.initAjaxLinks();
        self.initPopupBtn();
    }
}

var DCF = new DCFramework();
$( document ).ready(function() {
    DCF.init();
});