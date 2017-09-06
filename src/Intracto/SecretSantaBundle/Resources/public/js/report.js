var defaultChartOptions = {
    height: 300,
    legend: {position: 'bottom'},
    colors: ['#c41425'],
    backgroundColor: { fill:'transparent' }
};

google.charts.load('current', {'packages':['corechart', 'bar']});

function drawPartyChart() {
    $.ajax({
        url: 'report/season-party-data/2017',
        dataType: 'json',
    }).done(function (results) {
        var data = new google.visualization.DataTable();

        data.addColumn('string', 'Month');
        data.addColumn('number', 'Parties');

        $.each(results, function (i, row) {
            data.addRow([
                row.month,
                parseInt(row.accumulatedPartyCountByMonth)
            ]);
        });

        var chart = new google.visualization.ColumnChart(document.getElementById('party_chart'));

        chart.draw(data, defaultChartOptions);
    });
}

function drawParticipantChart() {
    $.ajax({
        url: 'report/season-participant-data/2017',
        dataType: 'json',
    }).done(function (results) {
        var data = new google.visualization.DataTable();

        data.addColumn('string', 'Month');
        data.addColumn('number', 'Participants');

        $.each(results, function (i, row) {
            data.addRow([
                row.month,
                parseInt(row.accumulatedParticipantCountByMonth)
            ]);
        });

        var chart = new google.visualization.ColumnChart(document.getElementById('participant_chart'));

        chart.draw(data, defaultChartOptions);
    });
}

function drawTotalParticipantChart() {
    $.ajax({
        url: 'report/total-season-participant-data/2017',
        dataType: 'json',
    }).done(function (results) {
        var data = new google.visualization.DataTable();

        data.addColumn('string', 'Month');
        data.addColumn('number', 'Participants');

        $.each(results, function (i, row) {
            data.addRow([
                row.month,
                parseInt(row.totalParticipantCount)
            ]);
        });

        var chart = new google.visualization.LineChart(document.getElementById('total_participant_chart'));

        chart.draw(data, defaultChartOptions);
    });
}

function drawTotalPartyChart() {
    $.ajax({
        url: 'report/total-season-party-data/2017',
        dataType: 'json',
    }).done(function (results) {
        var data = new google.visualization.DataTable();

        data.addColumn('string', 'Month');
        data.addColumn('number', 'Parties');

        $.each(results, function (i, row) {
            data.addRow([
                row.month,
                parseInt(row.totalPartyCount)
            ]);
        });

        var chart = new google.visualization.LineChart(document.getElementById('total_party_chart'));

        chart.draw(data, defaultChartOptions);
    });
}


// call drawChart once google charts is loaded
google.charts.setOnLoadCallback(drawPartyChart);
google.charts.setOnLoadCallback(drawParticipantChart);
google.charts.setOnLoadCallback(drawTotalPartyChart);
google.charts.setOnLoadCallback(drawTotalParticipantChart);

