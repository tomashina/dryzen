window.poipInstalled = true;

var poipModActive = false,
    notPoipModImage;

poip.showImages = function showImages(el,row,data)
    {
      if ( poip.notIntializedWillCallLater( showImages, arguments) || !poip.product_options.length)
      {
        return;
      }

      var html = '';
      var checkbox_names = [];
      
      poip.each(poip.product_options, function(product_option){
      //for (var i in poip.product_options) {
      //  if ( !poip.product_options.hasOwnProperty(i) ) continue;
        
        //var product_option = poip.product_options[i];
        
        if ( $.inArray(product_option.type, ['select', 'radio', 'image', 'checkbox', 'color', 'block']) != -1) {
          
          html+= '<div class="text-left poip-option-to-image">';
          html+= '<b>'+product_option.name+'</b><br>';
          
          var checkbox_name = 'product_image[index][poip]['+product_option.option_id+'][]';
          checkbox_names.push(checkbox_name);
          
          poip.each(product_option.product_option_value, function(product_option_value){
          //for (var j in product_option.product_option_value) {
          //  if ( !product_option.product_option_value.hasOwnProperty(j) ) continue;
          //
          //  var product_option_value = product_option.product_option_value[j];

            html+= '<div class="checkbox" >';
            html+= '<label>';
            html+= '<input type="checkbox" name="' + checkbox_name + '" value="' + product_option_value.option_value_id + '"';
            if (data && data.poip && data.poip[product_option.option_id]) {
              if ( $.inArray(product_option_value.option_value_id, data.poip[product_option.option_id]) != -1 ) {
                html+= ' checked ';
              }
            }
            html+= '>&nbsp;'+poip.getProductOptionValueName(product_option.option_id, product_option_value.option_value_id);
            html+= '</label>';
            html+= '</div>';
            
            html+= '';
            html+= '';
          });
          
          // no value
          html+= '<div class="checkbox" >';
          html+= '<i><label title="'+poip.texts.entry_no_value+'">';
          html+= '<input type="checkbox" name="'+checkbox_name+'" value="0"';
          if (data && data.poip && data.poip[product_option.option_id]) {
            if ( $.inArray(0, data.poip[product_option.option_id]) != -1 ) {
              html+= ' checked ';
            }
          }
          html+= '>&nbsp;'+poip.texts.entry_no_value;
          html+= '</label></i>';
          html+= '</div>';
          
          html+= '</div>';
        }
      });

      var oldPoipMod = $(el).find('.poip-mod');

      if(oldPoipMod.length)
        oldPoipMod.remove();

      $(el).append('<div class = "poip-mod ' + (poipModActive ? " active" : "") + '" ><i class="fa fa-filter" aria-hidden="true"></i>' + html + '</div>');
      
      for ( var i_checkbox_names in checkbox_names ) {
        if ( !checkbox_names.hasOwnProperty(i_checkbox_names) ) continue;
        var checkbox_name = checkbox_names[i_checkbox_names];
        poip.setAvailabilityOfNoValueByName(checkbox_name);
      }
    }


  $('#list-view').on('click',function (e)
  {
    if (e.target.className == 'fa fa-filter')
    {
      if(!poipModActive)
      {
        $('.poip-mod').addClass('active');
        poipModActive = true;
      }
      else
      {
        $('.poip-mod').removeClass('active');
        poipModActive = false;
      }
    }
    
  })

  $('#list-view .image').each(
    function(ind,el)
    {
      if(ind)
        poip.showImages(el,ind,$(el).data().poip);
      else
        notPoipModImage = el;
    });
