var BZ = {
		/* Imagens que devem ser pré-carregadas */
			'preloadImgs': {
		            'loading': "/img/loading.gif",
		    },
		            
		    'preloadImgAction' : function() {
									for (i in BZ.preloadImgs)
									{
										var img = new Image();
										img.src = eval("BZ.preloadImgs." + i);
        					
									}
			},
}

/* Tela de loading fullscreen */
BZ.showLoadingScreen = function(msg) {
	
	if (typeof(msg) != 'string')
	{
		msg = "Loading..."
	}
	
	$("BODY").append("<div id='loadingscreen-overlay' class='modal-overlay' style='opacity:.85'></div>");
	$("BODY").append("<div id='loadingscreen-container'><img src='"+BZ.preloadImgs.loading+"' alt='||.' /><br><br><span style='font-face:Ubuntu'>"+msg+"</span></div>");
	$('BODY').addClass('noClick');
	
}
BZ.hideLoadingScreen = function() {
	$("DIV[id*=loadingscreen]").remove();
	$('BODY').removeClass('noClick');
}

/* notificationCounterMemory */
BZ._ncm = {}

BZ.notificationCounter = function (action, type, path) {
	
	if (typeof(action) == 'undefined')
	{
		return false
	}

	if (typeof(type) == 'undefined')
	{
		// a = all
		type = 'a';
	}
	
	if (typeof(window._BZ_ncc) == 'undefined')
	{
		window._BZ_ncc = 0
	}
	
	switch (action)
	{
		case 'rm':
			BZ._ncm[type] = 0
			BZ.notificationCounter('get',null,path);
			if (typeof(path) != 'undefined')
			{
				$(path + ' .i'+type+' .counter').hide()
			
				$(path + ' .total').text(parseInt($(path + ' .total').text()) - parseInt($(path + ' .i'+type+' .counter').text()))
				if (parseInt($(path + ' .total').text()) == 0)
				{
					$(path + ' .total').hide()
				}
			}
		break;
		
		default:
			if (BZ.isFocused() == true)
			{
			$.getJSON(BZ.baseUrl + '/system/notificationAlert',{rm:JSON.stringify(BZ._ncm),n:window._BZ_ncc}, function(data){
				if (typeof(path) != 'undefined')
				{
					var total = 0;
					var response = false;
					for (var i in data.response)
					{
						response = true;
						total+= data.response[i];
						$(path + ' .i'+i+' .counter').text(data.response[i])
						if (data.response[i] > 0)
						{
							$(path + ' .i'+i+' .counter').show()
						}
						else
						{
							$(path + ' .i'+i+' .counter').hide()
						}
					}
					if (response == true)
					{
						$(path + ' .total').text(total)
						if (total > 0)
						{
							$(path + ' .total').show()
						}
						else
						{
							$(path + ' .total').hide()
						}
					}
				}
				
			});
			BZ._ncm = {};
			window._BZ_ncc+= 1;
			}
		break;
	}
}

// Checks bazzapp is fucused or not, can be used to stop some loop events
BZ.isFocused = function() {return window._BZ_wf; }


// Checks bazzapp email on paypal
BZ.checkPaypalEmailAddress = function(url) {
	
	if (typeof(url) == 'undefined') {
		return false;
	}

		var _checkPaypalEmail = setInterval(function() {
			$.getJSON(url, null, function(data) {
				if (data.emailStatus == 1) {
					clearInterval(_checkPaypalEmail);
					$('#paypalOkDisplay').removeClass('hide');
					$('#paypalWaitingDisplay').addClass('hide');
					parent.window.location = '/sell';
				}
				
			});
		} ,3000)
	
	
	return false;
}

// jQuery page done
$(document).ready(function(){
	// Preload images
	BZ.preloadImgAction()

	// Verifica se há algum novo evento.
	setInterval("BZ.notificationCounter('get',null,'#menu .menu-account')", 5000);
	
	// Aplica máscaras
	_bzMaskInput()
	
	// Event for all follow button
	$('.btnFollow').click(function(e)
	{
		e.preventDefault();
		window.BZ_btnFollow = this;
		$.ajax({
			url:this.href,
			beforeSend: function() {
				$(window.BZ_btnFollow).html("<span>&nbsp;&nbsp;<img src='"+BZ.preloadImgs.loading+"' heigth='20px' width='20px' />&nbsp;&nbsp;</span>");
			},
			success: function(a,b,c){
				if (a == "")
				{
//					$(window.BZ_btnFollow).removeClass("selected");
//					$(window.BZ_btnFollow).parent().parent().parent().removeClass("actunfollow");
//					$(window.BZ_btnFollow).parent().parent().parent().addClass("actfollow");
					$(window.BZ_btnFollow).attr('href', $(window.BZ_btnFollow).attr('href').replace('/unfollow','/follow'))
					// TODO: Usar a string do dicionario locale quando houver
					// $(window.BZ_btnFollow).html("<span>"+__('Layouts.default.controls.follow')+"</span>");
					$(window.BZ_btnFollow).removeClass("btn2_2");
					$(window.BZ_btnFollow).removeClass("unfollow");
					$(window.BZ_btnFollow).addClass("follow");
					$(window.BZ_btnFollow).html("<span>Seguir</span>");
				}
				else
				{
//					$(window.BZ_btnFollow).addClass("selected");
//					$(window.BZ_btnFollow).parent().parent().parent().removeClass("actfollow");
//					$(window.BZ_btnFollow).parent().parent().parent().addClass("actunfollow");
					$(window.BZ_btnFollow).attr('href', $(window.BZ_btnFollow).attr('href').replace('/follow','/unfollow'))
					// TODO: Usar a string do dicionario locale quando houver
					// $(window.BZ_btnFollow).html("<span>"+__('Layouts.default.controls.unfollow')+"</span>");
					$(window.BZ_btnFollow).addClass("btn2_2");
					$(window.BZ_btnFollow).removeClass("follow");
					$(window.BZ_btnFollow).addClass("unfollow");
					$(window.BZ_btnFollow).html("<span>Seguindo</span>");
				}
				
				FB.api(
						"/me/"+BZ.appName+":follow",
						"post",
						{ store: $(window.BZ_btnFollow).attr('href'), 
						  access_token: BZ.accessTokenFB
						},
						function(response) {
						   if (!response || response.error) {
								console.log("Erro! " + response.error.message);
						   } else {
							  console.log("Sucesso! Action ID: " + response.id);
						   }
						});
			}
		})

	})
	
	
	var formRegisterProduct = {
				rules: {
					"data[paypalAdaptiveAccount][firstName]" : "required",
					"data[paypalAdaptiveAccount][lastName]" : "required",
					"data[paypalAdaptiveAccount][paypalEmail]" : {required: true, email:true},
					"data[paypalAdaptiveAccount][birthday]" : {required: true, date: true},
					"data[paypalAdaptiveAccount][zipcode]" : {required:true, notEmpty:true},
					"data[paypalAdaptiveAccount][uniqueIdentifierNumber]" :  {required: true, validCpf: true, notEmpty:true},
					"data[paypalAdaptiveAccount][address]": {required:true, notEmpty:true},
					"data[paypalAdaptiveAccount][addressLine2]": {required:true, notEmpty:true},
					"data[paypalAdaptiveAccount][district]": {required:true, notEmpty:true},
					"data[paypalAdaptiveAccount][city]": {required:true, notEmpty:true},
					"data[paypalAdaptiveAccount][state]":{required: true, notEmpty: true},
					"data[paypalAdaptiveAccount][phone]":{required: true, notEmpty: true},
				},
				messages: {
					"data[paypalAdaptiveAccount][firstName]" : "Informe seu primeiro nome",
					"data[paypalAdaptiveAccount][lastName]" : "Informe seu último nome",
					"data[paypalAdaptiveAccount][paypalEmail]" : {required: "Informe o email para ser usado no PayPal", email:"Informe um email válido"},
					"data[paypalAdaptiveAccount][birthday]" : {required: "Infome sua data de nascimento", date: "Infome uma data válida (dd/mm/aaaa)"},
					"data[paypalAdaptiveAccount][zipcode]" : {required:"Infome seu CEP", notEmpty:"Informe seu CEP"},
					"data[paypalAdaptiveAccount][uniqueIdentifierNumber]" :  {required: "Infome seu CPF", validCpf: "Infome um CPF válido", notEmpty: "Infome um CPF válido"},
					"data[paypalAdaptiveAccount][address]": {required: "Infome o seu endereço (logradouro e número)", notEmpty:"Infome o seu endereço (logradouro e número)"},
					"data[paypalAdaptiveAccount][addressLine2]": {required: "Informe o complemento de endereço", notEmpty: "Informe o complemento de endereço"},
					"data[paypalAdaptiveAccount][district]": {required: "Infome seu bairro", notEmpty: "Infome seu bairro"},
					"data[paypalAdaptiveAccount][city]": {required: "Informe sua cidade", notEmpty: "Informe sua cidade"},
					"data[paypalAdaptiveAccount][state]":{required: "Informe sua UF", notEmpty: "Informe sua UF"},
					"data[paypalAdaptiveAccount][phone]":{required: "Informe seu telefone", notEmpty: "Informe seu telefone"},
				},
				submitHandler: function() {
					$("label.error").hide();
					BZ.showLoadingScreen("Enviando seus dados");
					$("#AAPaypalAdaptiveAccountActionForm")[0].submit();
					
				},
				
				validHandler: function() {
					$("label.error").hide();
					$('.btnPaypalSubmit').addClass('disabled');
				},
				invalidHandler: function() {
					BZ.hideLoadingScreen();
				},
				
				debug: true
		};	
	

	$.validator.addMethod("notEqualTo", function(value, element, param) {
			return this.optional(element) || value != $(param).val();
	}, "This has to be different...");
		
	$.validator.addMethod("notEmpty", function( value, element ) {
		 if (value == 0 || value == '' || value == null || value == 'undefined' || value == undefined) {
			 return false;
		 } else {
			 return true;
		 }
}, "This cannot be emapty");

	$.validator.addMethod("validCpf", function( value, element ) {
			 value = value.replace('.','');
			    value = value.replace('.','');
			    cpf = value.replace('-','');
			    while(cpf.length < 11) cpf = "0"+ cpf;
			    var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
			    var a = [];
			    var b = new Number;
			    var c = 11;
			    for (i=0; i<11; i++){
			        a[i] = cpf.charAt(i);
			        if (i < 9) b += (a[i] * --c);
			    }
			    if ((x = b % 11) < 2) { a[9] = 0;} else { a[9] = 11-x;}
			    b = 0;
			    c = 11;
			    for (y=0; y<10; y++) b += (a[y] * c--);
			    if ((x = b % 11) < 2) { a[10] = 0; } else { a[10] = 11-x; }
			    if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg)) return false;
			    return true;
	});
		
	$.validator.addMethod("date", function(value, element) {
		     //contando chars
		    if(value.length!=10) return false;
		    // verificando data
		    var data        = value;
		    var dia         = data.substr(0,2);
		    var barra1      = data.substr(2,1);
		    var mes         = data.substr(3,2);
		    var barra2      = data.substr(5,1);
		    var ano         = data.substr(6,4);
		    if(data.length!=10||barra1!="/"||barra2!="/"||isNaN(dia)||isNaN(mes)||isNaN(ano)||dia>31||mes>12)return false;
		    if((mes==4||mes==6||mes==9||mes==11)&&dia==31)return false;
		    if(mes==2 && (dia>29||(dia==29&&ano%4!=0)))return false;
		    if(ano < 1900)return false;
		    return true;
	}, "Informe uma data válida");
		
	
	// Validate forms
	if ($("#AAPaypalAdaptiveAccountActionForm").length>0) {
		$("#AAPaypalAdaptiveAccountActionForm").validate(formRegisterProduct);
	}	
	
	// Checa se a janela tem foco
	$(window).focus(function() {window._BZ_wf = true;}).blur(function() {window._BZ_wf = false;});
})



// Masks for inputs
var _bzMaskInput = function() {
	$("INPUT.date").mask("99/99/9999");
	$("INPUT.phone").mask("(99) 9999-9999?9");
	$("INPUT.tel").mask("(99) 9999-9999?9");
	$("INPUT.cpf").mask("999.999.999-99");
	$("INPUT.cnpj").mask("99.999.999/9999-99");
	$("INPUT.cep").mask("99999-999");
	$("INPUT.percent100").mask("99,99%");
	// Formata monetariamente os campos com classe currency 999.999,99
	$('.currency').keydown(function(e){
		var k=e.which;
		
		if (k==13) {
			if (this.type == 'text' || this.type == 'email' || this.type == 'tel') {
				$(this).closest('form').submit()
			}
		} else if ((this.value.length>=14 && k!= 8 && k!= 9 && k != 46 && k != 127) ) {
		
			return false;
			
		} else if(k != 8 && k != 9 && k != 46 && k != 127 && k != 39 && k != 37){
			// Se for DELETE ou BACKSPACE não faz nada
		
		} else if (k == 27) {//escape
			this.selectionStart = 0;
			this.selectionEnd = this.value.length-1;
			return false;
		} else if ( k != 39 && k != 37) {
			// Marca o evento de keyup
			this.ku = true;
		}
	}).keyup(function(e){
		var k=e.which;		
		if ( k != 39 && k != 37) {
		// Remove qq que não seja numero ou pontuação
			pos = this.selectionStart;
			this.value = this.value.replace(/([^0-9.,]+)/g,"");
		
			// Caso o flag para keyup esteja marcado, tratar dados
			this.ku = false;
			var v = this.value.replace(/([^0-9]+)/g,"");
			v = v.split("");
		
			if (v.length>=2) {
				v[v.length-3]+= ",";
			}
			var sepw = parseInt((v.length-3)/3);
			
			for (var i=0;i<sepw;i++) {
					v[v.length-(3+((i+1)*3))]+= ".";
			}
			this.value = v.join("");
			this.selectionStart = pos;
			this.selectionEnd = pos;
		}
	});
}