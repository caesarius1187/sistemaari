$(function () {
    $('#ganancia').change(function(){
        calcularProducto();
    });
    $('#gananciapack').change(function(){
        calcularProducto();
    });
    $('#ganancia1').change(function(){
        calcularProducto();
    });
    $('#ganancia2').change(function(){
        calcularProducto();
    });
    $('#ganancia3').change(function(){
        calcularProducto();
    });
    $('#ganancia4').change(function(){
        calcularProducto();
    });
    $('#costo').change(function(){
        calcularProducto();
    });
    $('#cantpack').change(function(){
        calcularProducto();
    });
    catchPreciosChange();
    //calcularProducto();
  });
 
    function calcularGanancia(ganancia,precio,preciopack,costo){
        var precio = $('#'+precio).val();
        var costo = $('#'+costo).val();
        var porcentaje = precio/costo;
        porcentaje -= 1;
        porcentaje = porcentaje*100;
        $('#'+ganancia).val((porcentaje).toFixed(2));
         if(preciopack!=''){
            var cantidad = $("#cantpack").val();
            var preciopackcalculado = cantidad*precio;
            $('#'+preciopack).val((preciopackcalculado).toFixed(2));
        }
     }
    function calcularPrecioUnidad(ganancia,precio,preciopack,costo){
        var preciopack = $('#'+preciopack).val();
        var cantidadPack = $('#cantpack').val();
        var preciocalculado = preciopack/cantidadPack;
        var costo = $('#'+costo).val();
        var porcentaje = preciocalculado/costo;
        porcentaje -= 1;
        porcentaje = porcentaje*100;
        $('#'+ganancia).val((porcentaje).toFixed(2));
        $('#'+precio).val((preciocalculado).toFixed(2));
     }
    function catchPreciosChange(){
        $('#precio').change(
           function(){
              calcularGanancia('ganancia','precio','','costo');
        });
        $('#preciopack').change(
           function(){
              calcularGanancia('gananciapack','preciopack','preciopack0','costo');
        });
        $('#preciomayor1').change(
           function(){
              calcularGanancia('ganancia1','preciomayor1','preciopack1','costo');
        });
        $('#preciomayor2').change(
           function(){
              calcularGanancia('ganancia2','preciomayor2','preciopack2','costo');
        });
        $('#preciomayor3').change(
           function(){
              calcularGanancia('ganancia3','preciomayor3','preciopack3','costo');
        });
        $('#preciomayor4').change(
           function(){
              calcularGanancia('ganancia4','preciomayor4','preciopack4','costo');
        });                
        $('#preciopack0').change(
            function(){
               calcularPrecioUnidad('gananciapack','preciopack','preciopack0','costo')
           }
        );
        $('#preciopack1').change(
            function(){
               calcularPrecioUnidad('ganancia1','preciomayor1','preciopack1','costo')
           }
        );
        $('#preciopack2').change(
            function(){
               calcularPrecioUnidad('ganancia2','preciomayor2','preciopack2','costo')
           }
        );
        $('#preciopack3').change(
            function(){
               calcularPrecioUnidad('ganancia3','preciomayor3','preciopack3','costo')
           }
        );
        $('#preciopack4').change(
            function(){
               calcularPrecioUnidad('ganancia4','preciomayor4','preciopack4','costo')
           }
        );
    }
    function calcularProducto(){
        var costo = $('#costo').val();
        var ganancia = $('#ganancia').val();
        var cantidad = $('#cantpack').val();
        var precio = costo*(1+(ganancia/100));
        $('#precio').val(Math.round(precio));
        var gananciapack = $('#gananciapack').val();
        var preciopack = costo*(1+(gananciapack/100));
        var preciopackcalculado = preciopack*cantidad;
        $('#preciopack').val(Math.round(preciopack));
        $('#preciopack0').val(Math.round(preciopackcalculado));
    
        var ganancia1 = $('#ganancia1').val();
        var preciopack1 = costo*(1+(ganancia1/100));
        $('#preciomayor1').val(Math.round(preciopack1));
        preciopackcalculado = preciopack1*cantidad;
        $('#preciopack1').val(Math.round(preciopackcalculado));
    
         var ganancia2 = $('#ganancia2').val();
        var preciopack2 = costo*(1+(ganancia2/100));
        $('#preciomayor2').val(Math.round(preciopack2));
        preciopackcalculado = preciopack2*cantidad;
        $('#preciopack2').val(Math.round(preciopackcalculado));
    
         var ganancia3 = $('#ganancia3').val();
        var preciopack3 = costo*(1+(ganancia3/100));
        $('#preciomayor3').val(Math.round(preciopack3));
        preciopackcalculado = preciopack3*cantidad;
        $('#preciopack3').val(Math.round(preciopackcalculado));
    
         var ganancia4 = $('#ganancia4').val();
        var preciopack4 = costo*(1+(ganancia4/100));
        $('#preciomayor4').val(Math.round(preciopack4));
        preciopackcalculado = preciopack4*cantidad;
        $('#preciopack4').val(Math.round(preciopackcalculado));
    }