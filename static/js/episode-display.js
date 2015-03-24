/**
 * Created by Antoine on 20/03/2015.
 */
var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}

function peer5Downloader(link, id, type) {
    //var linkPart = link.split('/');
    //var file = linkPart[linkPart.length -1];
    peer5.download(link, {
        onDownloadComplete: function () {
            $.ajax({
                type: "POST",
                url: "api/ddl.php",
                data: {
                    id: ep,
                    type: type,
                    downloaded: true
                },
                dataType: "json"
            }).success(function () {
                console.log("File downloaded");
            }).fail(function (e) {
                console.error(e);
            })
        }
    });
    getFeedback();
}

function checkDDL(e, options) {
    ep = $(this).attr('data-ep');
    qual = $(this).attr('data-qual');
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: "api/ddl.php",
        data: {
            id: ep,
            type: qual
        },
        dataType: "json"
    }).success(function (result) {
        var decoded = Base64.decode(result.dlpath);
        peer5Downloader(decoded, ep, qual);
    }).fail(function (e) {
        console.error(e);
        alert("Le lien DDL ne marche pas, essayer l'autre lien");
    });

}

function generateLink(id, quality, classes, text) {
    return ('<a data-ep="{0}" data-qual="{1}" target="_blank" class="'+classes+'" href="dl-{0}-{1}.html">'+text+'</a>').split('{0}').join(id).split("{1}").join(quality);
}

function renderLink(data, row, quality) {
    if (data) {
        if(row['DDL_'+quality]) {
            var liens = generateLink(row.id, quality, 'ddl', 'DDL AnT');
            liens += generateLink(row.id, quality, 'dl', 'Jheberg');
            return liens;
        }
        return generateLink(row.id, quality, 'dl', 'Jheberg');
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
            $('#episodes').on('click', 'a.ddl', checkDDL);
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