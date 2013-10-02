// DOM #####
$(document).ready(function(){ 

	if ($('#global').hasClass('home')) { // HOME
		if((!$('#global').hasClass('fanpage')) && (!$('#global').hasClass('search'))){		
			if ($('#home-carousel').hasClass('store')) { // CAROUSEL		
				$('#carousel').bxSlider({ minSlides: 2,maxSlides: 5,moveSlides: 1,auto: true});
			}else{
				$('#carousel').bxSlider({ minSlides: 3,maxSlides: 15,moveSlides: 3,auto: true});
			}
		}
	} else if ($('#global').hasClass('product')) { // PRODUCT
		$('#global').addClass('store');	
		$('.gal_thumb ul li:first-child').addClass('selected');
		$('.gal_thumb ul li a').click(function(e){
			e.preventDefault();											   
			var linkz = $(this).attr('name');
			$('.gal_thumb ul li').removeClass('selected');
			$(this).parent().addClass('selected');
			$('.gal_large ul li').hide();
			$('.gal_large ul li.'+linkz).show();
		});											   
		
		if ($('#global').hasClass('view')) {
				$('#multizoom2').addimagezoom({
					descArea: '#description2',
					disablewheel: true,
					zoomrange: [2, 2],
					magnifiersize: [300,300],
					magnifierpos: 'right',
					cursorshade: true				
				});
		}

		if($('input').hasClass('qtd_aval')) {
			$('.infop > div.qtd > a').click(function(e){
					e.preventDefault();
					var now_quantity = parseInt($('input.qtd_aval').val());
					
					if($(this).hasClass('mais')){
						vlx = now_quantity+1;
						if(product_quantity_available>=vlx){ $('input.qtd_aval').val(vlx); }
					}else{
						vlx = now_quantity-1;						
						if(vlx!=0){ $('input.qtd_aval').val(vlx); }
					}
			});											   
	
			$('input.qtd_aval').blur(function() {
				var	now_quantity = parseInt($('input.qtd_aval').val());
				if((now_quantity > product_quantity_available) || (now_quantity == 0)){ $('input.qtd_aval').val('1'); }
			});											   										  
	
			if ($('#global').hasClass('buy')) { $(".maskCep").mask("99999-999"); } // BUY
		}

	}else if ($('#global').hasClass('how-works')) { // HOW WORKS
		$('fieldset > div').click(function() { $(this).next('p').slideToggle(); });

	}else if ($('#global').hasClass('edit')) { // SELL ADD/EDIT
		$('a.add-more-photo').click(function(e) { e.preventDefault(); $(this).next('div').toggle(); });
		if($('div').hasClass('collapse')){
			$('div.collapse > div').click(function() { $(this).next('p').slideToggle(); });
		}
			
	}
	
	fdefault();
});


function fdefault(){

	menus();
	searchVal();

	// Go top
	if (typeof(FB) != "undefined" && FB !== null){ FB.Canvas.scrollTo(0,0); FB.Canvas.setSize(); }
	
	// Inputs, Forms
	$("select, input:checkbox, input:radio").filter(":not(.nostyle)").uniform(); //, input:file //	input:radio(:not(.nostyle))
	if ($(".tooltip").length > 0) {
		$(".tooltip").tipTip();
	}
	$('input.maskmoney').maskMoney({thousands:''});
	$('input.masknumber').numeric(false);
	
}

function menus(){
	// Top Menu
	$("#menu > div").hover(function(){ $(this).children("div").show() }, function(){ $(this).children("div").hide(); });	
	
	$(".menu-categories").hover(function(){ 
		$(this).addClass("selected");
		$(this).children("div.categories").children("div#nav_cats").show();
		$("#nav_cats > ul > li").hover(function(){
			$("#nav_cats > ul > li").removeClass("selected");
			$(this).addClass("selected");
			itemId = "#"+$(this).attr('id').replace("cat","subcat"); ;
			$("#nav_subcats > div").hide();
			$(itemId).show();
			if ($(itemId).hasClass('notEmpty'))
			{
				$("#nav_subcats").show();
				$("#nav_subcats").css('top',$(this).position().top+'px');
			}
			else
			{
				$("#nav_subcats").hide();
			}
		})
	}, function(){ 
		$(this).removeClass("selected");	
		$("#nav_cats > ul > li").removeClass("selected");
		$(this).children("div.categories").children("div#nav_cats").hide(); 
		$(this).children("div.categories").children("div#nav_subcats").hide(); 		
	});		
}

function searchVal(){
	$("input.search-field").focus(function(){ if(($(this).val()=="") || ($(this).val()==$(this).attr("title"))){ $(this).val(""); } });
	$("input.search-field").blur(function() { if($(this).val()==""){ $(this).val($(this).attr("title")); } });	
}