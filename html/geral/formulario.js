function disableForm(idForm){
    if (!idForm) return false;
    if (!$("#"+idForm).length) console.warn("Falha ao encontrar o formulário com o seguinde id: "+idForm);
    $("#"+idForm+" :input").each(function(){
        var input = $(this);
        if (input.prop('tagName') != "BUTTON"){
            input.prop('disabled', true);
            input.prop('readOnly', true);
        }
    });
}

function enableForm(idForm){
    if (!idForm) return false;
    if (!$("#"+idForm).length) console.warn("Falha ao encontrar o formulário com o seguinde id: "+idForm);
    $("#"+idForm+" :input").each(function(){
        var input = $(this);
        if (input.prop('tagName') != "BUTTON"){
            input.prop('disabled', false);
            input.prop('readOnly', false);
        }
    });
}

function getFormPostParams(idForm){
    if (!idForm) return false;
    if (!$("#"+idForm).length) console.warn("Falha ao encontrar o formulário com o seguinde id: "+idForm);
    var string = "";
    var first = true;
    var mapObj = {
        "+":"%2B",
        "%":"%25",
        "&": "%26",
        "$": "%24",
        "#": "%23",
        "@": "%40",
        "?": "%3F"
    };
    $("#"+idForm+" :input").each(function(){
        var input = $(this);
        var nome = input.attr('name');
        var value = input.val().replace(/\+|%|&|\$|#|@|\?/gi, function(matched){
            return mapObj[matched];
        });
        if ((input.prop('type') == 'radio' && !input.prop('checked')) || !nome){
            false;
        }else{
            if (input.prop('required') && (!value || value == 'null')){
                string = false;
                return false;
            }
            string += ((first ? "" : "&")+nome+"="+ value || "");
            first = false;
        }
    });
    return string;
}