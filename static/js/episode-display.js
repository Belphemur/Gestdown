/**
 * Created by Antoine on 20/03/2015.
 */

function generateLink(id, quality) {
    return '<a target="_blank" class="dlLink" href="dl-{0}-{1}.html">Lien</a>'.split('{0}').join(id).split("{1}").join(quality);
}
function renderLink(data, row, quality) {
    if (data) {
        return generateLink(row.id, quality);
    }
    return "";
}
function getEpNumber(subject) {
    var myregexp = /Episode (\d{1,3})/;
    var match = myregexp.exec(subject);
    var result;
    if (match != null) {
        result = match[1];
    } else {
        result = "";
    }
    return parseInt(result);
}
$.extend($.fn.dataTableExt.oSort, {
    "episode-asc": function (a, b) {
        var first = getEpNumber(a),
            second = getEpNumber(b);
        return ((first < second) ? -1 : ((first > second) ? 1 : 0));
    },
    "episode-desc": function (a, b) {
        var first = getEpNumber(a),
            second = getEpNumber(b);
        return ((first < second) ? 1 : ((first > second) ? -1 : 0));
    }
});

$.fn.dataTable.Api.register('hideColumns()',
    function () {
        /**
         * This plugin hides the columns that are empty.
         * If you are using datatable inside jquery tabs
         * you have to add manually this piece of code
         * in the tabs initialization
         * $("#mytable").datatables().fnAdjustColumnSizing();
         * where #mytable is the selector of table
         * object pointing to this plugin.
         * This plugin can be invoked from
         * <a href="//legacy.datatables.net/ref#fnInitComplete">fnInitComplete</a> callback.
         * @author John Diaz
         * @version 1.0
         * @date 06/28/2013
         */

        var table = this.table();
        $(table.node()).find('th').each(function (i) {

            var rows = $(this).parents('table').find('tr td:nth-child(' + (i + 1) + ')'); //Find all rows of each column
            var rowsLength = $(rows).length;
            var emptyRows = 0;

            rows.each(function (r) {
                if (this.innerHTML == '')
                    emptyRows++;
            });

            if (emptyRows == rowsLength) {
                $(this).addClass("never");
            }
        });

    });


$(document).ready(function () {
    var $records = $('#jsonepisodes'),
        myRecords = JSON.parse($records.text());
    var table = $('#episodes').dataTable({
        data: myRecords,
        responsive: true,
        initComplete: function () {
            var api = this.api();
            api.table().hideColumns();
            if($("#episodeId").length) {
                var id = parseInt($("#episodeId").text());
                $('#ep-'+id).addClass("epSelected");
                $("html, body").animate({
                    'scrollTop':   $('#ep-'+id).offset().top
                }, 2000);
            }
        },
        "columns": [
            {"data": "nombre", "type": "episode"},
            {"data": "id", visible:false ,className: "never"},
            {"data": "titre"},
            {"data": "dl"},
            {
                "data": "mq", render: function (data, type, row) {
                return renderLink(data, row, 'mq');
            }
            },
            {
                "data": "hd", render: function (data, type, row) {
                return renderLink(data, row, 'hd');
            }
            },
            {
                "data": "fhd", render: function (data, type, row) {
                return renderLink(data, row, 'fhd');
            }
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
        }, "createdRow": function (row, data) {
            $(row).attr("id", "ep-" + data.id);
        },
        "paging": false

    });
});