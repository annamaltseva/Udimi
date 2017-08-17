$(function(){
    $.ajax({
        url: '/cells/get',
        method: 'GET'
    })
    .done(function(e){
        var cid      = 1,     //Cell position id start
            value    = 1,     //Start value for 1'st cell
            maxVal   = 99999, //Maximum possible Value
            cellsX   = 100,   //Set cells rows
            cellsY   = 100,   //Set cells columns
            data     = [],
            dataSet  = [],
            table    = '',
            cells    = (e.length > 0) ? e : null, // init request parse result
            cellVal,
            selected = [];

        if(cells !== null) {
            $.each(cells, function (i, cell) {
                data[cell.cid] = cell.value;
            });
        }

        for(x = 1; x <= cellsX; x++){
            table += '<div class="cellsRow">';
            for( y = 1; y <= cellsY; y++){
                cellVal = (data[cid] !== undefined) ? data[cid] : cid;
                table += '<div class="cell">' +
                    '<input type="text" id="' + cid + '" value="' + cellVal + '" initVal="' + cellVal + '">' +
                    '</div>';
                cid++;
            }
            table += '</div>';
        }

        $('#table').html(table);

        $('.cell input')
            .keyup(function(){

                this.value = this.value.replace(/[^0-9]/,'');

                if(this.value > maxVal)
                    this.value = this.value.replace(/[\d+]+/,maxVal);

                var cid      = $(this).attr('id'),
                    cval     = $(this).val(),
                    initVal  = $(this).attr('initval'),
                    color    = cval !== '' ? (initVal == cval ? 'white' : 'green') : 'red',
                    objCheck = 0;

                $(this).css('background-color', color);

                objCheck = $.grep(dataSet, function(r){ return r.cid == cid});

                if(objCheck.length == 0 && cval !== initVal){
                    dataSet.push({'cid':cid, 'value':cval});
                }else{
                    if(cval !== initVal){
                        objCheck[0].value = cval;
                    }else{
                        dataSet = dataSet.filter(function(cell){
                            return cell.cid !== cid;
                        })
                    }
                }

                if(dataSet.length > 0){
                    $('#update').attr('disabled',false);
                }else{
                    $('#update').attr('disabled',true);
                }

            });

        $('#update').click(function(){
            $('#update').attr('disabled',true);
            $('.cell input').css('background-color','white');
            $.ajax({
                url: 'cells/set',
                method: 'POST',
                data: {'dataSet': dataSet}
            }).done(function(e){
                console.log(e);
                if(e.update == true){
                    $('span.success').show();
                }else{
                    $('span.error').show();
                    console.log(e.error);
                }
                $('#message').fadeIn(500);
                setTimeout(function(){
                    $('#message, span.error, span.success').fadeOut(500);
                },5000);
            });
        });

    });
})