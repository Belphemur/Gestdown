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

$(document).ready(function () {
    var $records = $('#jsonepisodes'),
        myRecords = JSON.parse($records.text());
    $('#episodes').dataTable({
        data: myRecords,
        "columns": [
            {"data": "nombre", "type": "episode"},
            {"data": "id", visible: false},
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