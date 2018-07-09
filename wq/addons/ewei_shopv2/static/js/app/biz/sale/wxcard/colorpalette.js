/**
 * bootstrap-colorpalette.js
 * (c) 2013~ Jiung Kang
 * Licensed under the Apache License, Version 2.0 (the "License");
 */

(function($) {
  "use strict";
  var aaColor = [
    [['#63B359','Color010'],['#2C9F67','Color020'],['#509FC9','Color030'],['#5885CF','Color040'],['#9062C0','Color050']],
    [['#D09A45','Color060'],['#E4B138','Color070'],['#EE903C','Color080'],['#DD6549','Color090'],['#CC463D','Color100']]
  ];

  var createPaletteElement = function(element, _aaColor) {
    element.addClass('bootstrap-colorpalette');
    var aHTML = [];
    $.each(_aaColor, function(i, aColor){
      aHTML.push('<div>');
      $.each(aColor, function(i, sColor) {
        var sButton = ['<button type="button" class="btn-color" style="background-color:', sColor[0],
          '" data-value="', sColor[0],
          '" data-colorname="', sColor[1],
          '" ></button>'].join('');
        aHTML.push(sButton);
      });
      aHTML.push('</div>');
    });
    element.html(aHTML.join(''));
  };

  var attachEvent = function(palette) {
    palette.element.on('click', function(e) {
      var welTarget = $(e.target),
          welBtn = welTarget.closest('.btn-color');

      if (!welBtn[0]) { return; }

      var value = welBtn.attr('data-value');
      var colorname = welBtn.attr('data-colorname');
      palette.value = value;
      palette.element.trigger({
        type: 'selectColor',
        color: value,
        element: palette.element
      });

      $("#color").val(colorname);
      $("#bgcolor").removeClass();
      $("#bgcolor").addClass("preview");
      $("#bgcolor").addClass(colorname);

      $("#btnuse").removeClass();
      $("#btnuse").addClass("btn");
      $("#btnuse").addClass(colorname);

    });
  };

  var Palette = function(element, options) {
    this.element = element;
    createPaletteElement(element, options && options.colors || aaColor);
    attachEvent(this);
  };

  $.fn.extend({
    colorPalette : function(options) {
      this.each(function () {
        var $this = $(this),
            data = $this.data('colorpalette');
        if (!data) {
          $this.data('colorpalette', new Palette($this, options));
        }
      });
      return this;
    }
  });
})(jQuery);
