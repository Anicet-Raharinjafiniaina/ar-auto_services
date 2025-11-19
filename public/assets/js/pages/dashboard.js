// --- Chart Montants des recettes ---
$('#periode_recette').on('change', function () {
    let recetteChart = null;
    const periode_recette = $(this).val();
    $.ajax({
        url: urlProject + "Dashboard/get_recette_chart_data",
        type: 'POST',
        data: {
            periode: periode_recette
        },
        dataType: 'json',
        success: function (response) {
            const dataArray = Array.isArray(response) ? response : response.data || [];
            const labels = dataArray.map(item => item.label);
            const data = dataArray.map(item => item.total);

            // Détruire l'ancien chart si existant
            // if (recetteChart) recetteChart.destroy();
            if (window.recetteChart !== undefined) {
                window.recetteChart.destroy(); // supprime l'ancien graphique
            }

            // Créer le nouveau chart
            recetteChart = new Chart(document.getElementById('recette_chart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'montant en Ariary',
                        data: data,
                        backgroundColor: '#1c84ee',
                        borderColor: '#1c84ee',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Montants ' + periode_recette
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => v.toLocaleString() + ' Ar'
                            }
                        }
                    }
                }
            });
        }
    });
});

// --- Chart Vente par article ---
$('#periode_article').on('change', function () {
    let articleChart = null;
    const periode_article = $(this).val();
    $.ajax({
        url: urlProject + "Dashboard/get_article_chart_data",
        type: 'POST',
        data: {
            periode: periode_article
        },
        dataType: 'json',
        success: function (response) {
            const dataArray = Array.isArray(response) ? response : response.data || [];

            // 1️⃣ Extraire toutes les dates uniques pour l'axe X
            const labels = [...new Set(dataArray.map(item => item.label))].sort();

            // 2️⃣ Grouper les données par article
            const grouped = {};
            dataArray.forEach(item => {
                if (!grouped[item.article_reference]) grouped[item.article_reference] = {};
                grouped[item.article_reference][item.label] = parseFloat(item.total);
            });

            // 3️⃣ Construire les datasets pour Chart.js
            const colors = ['#1c84ee', '#f8ac59', '#23c6c8', '#ed5565', '#5d9cec', '#ac92ec'];
            const datasets = Object.keys(grouped).map((article, index) => {
                const data = labels.map(label => grouped[article][label] || 0);
                return {
                    label: article,
                    data: data,
                    backgroundColor: colors[index % colors.length],
                    borderColor: colors[index % colors.length],
                    borderWidth: 1,
                    borderRadius: 5
                };
            });

            // 4️⃣ Détruire l'ancien chart si existant
            if (articleChart) articleChart.destroy();

            // 5️⃣ Créer le chart
            articleChart = new Chart(document.getElementById('article_chart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Vente par article (' + periode_article + ')'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
});

// --- Initialisation par défaut ---
$(document).ready(() => {
    $('#periode_recette').val('quotidien').trigger('change');
    $('#periode_article').val('quotidien').trigger('change');
});