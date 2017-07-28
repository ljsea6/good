$(document).ready(function() {
    var navListItems = $('div.setup-panel div a'),
        allWells = $('.setup-content'),
        allNextBtn = $('.nextBtn'),
        allPrevBtn = $('.prevBtn');
    allWells.hide();
    navListItems.click(function(e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
            $item = $(this);
        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            $item.addClass('btn-primary');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });
    allNextBtn.click(function() {
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url'],input[type='number'],input[type='password'],input[type='email'],select"),
            isValid = true;
            $(".form-group").removeClass("has-error");
            for (var i = 0; i < curInputs.length; i++) {
                if (!curInputs[i].validity.valid) {
                    isValid = false;
                    $(curInputs[i]).closest(".form-group").addClass("has-error");
                }

                if (curInputs[i].name.indexOf('telefono') != -1 || curInputs[i].name.indexOf('celular') != -1) { 
                    //console.log(curInputs[i].name.indexOf('telefono') + ' => '+ curInputs[i].value.length);
                    if (curInputs[i].value.length>10 || curInputs[i].value.length<7) {
                        isValid = false;
                        $(curInputs[i]).closest(".form-group").addClass("has-error");
                    }  
                }
            }
            if (isValid) nextStepWizard.removeAttr('disabled').trigger('click');
    });
    allPrevBtn.click(function() {
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");
        prevStepWizard.removeAttr('disabled').trigger('click');
    });
    $('div.setup-panel div a.btn-primary').trigger('click');

    //En todos los botones que tengan la capa guardar, esta funcion muestra en rojo los campos que estan sin validar antes de hacer la peticion request.
    $(".guardar").click(function() {
                curInputs = $('.forma').find("input[type='text'],input[type='url'],input[type='number'],input[type='password'],input[type='email'],input[type='select'],select"),
                isValid = true;
                $(".forma").removeClass("has-error");
                for (var i = 0; i < curInputs.length; i++) {
                    if (!curInputs[i].validity.valid) {
                        //console.log(curInputs[i].name + ' => '+ curInputs[i].validity.valid);
                        isValid = false;
                        $(curInputs[i]).closest(".form-group").addClass("has-error");
                        
                    }
                }
                if (!isValid) return false;
    });
});