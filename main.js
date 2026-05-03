function formatRupees(value) {
    value = Number(value);
    return value.toLocaleString("en-IN", {maximumFractionDigits: 2});
}

function monthName(num) {
    return ["January","February","March","April","May","June","July","August","September","October","November","December"][num-1] || num;
}

// --- Analysis Page ---
function updateAnalysis() {
    const type = document.getElementById('analysisType').value;
    document.getElementById('analysis-loading').style.display = '';
    document.getElementById('analysis-error').style.display = 'none';
    document.getElementById('products-loading').style.display = '';
    document.getElementById('products-error').style.display = 'none';
    document.getElementById('months-loading').style.display = '';
    document.getElementById('months-error').style.display = 'none';

    fetch('api/get_sales.php?type=' + type)
        .then(res => {
            if (!res.ok) throw new Error('API error');
            return res.json();
        })
        .then(data => {
            document.getElementById('analysis-loading').style.display = 'none';
            document.getElementById('products-loading').style.display = 'none';
            document.getElementById('months-loading').style.display = 'none';
            if (!data.bars.length) {
                document.getElementById('analysis-error').innerText = "No sales data found for the selected analysis.";
                document.getElementById('analysis-error').style.display = '';
            } else {
                renderChart(data.bars, type);
            }
            if (!data.topProducts.length) {
                document.getElementById('products-error').innerText = "No top products found.";
                document.getElementById('products-error').style.display = '';
            } else {
                renderTopProducts(data.topProducts);
            }
            if (!data.topMonths.length) {
                document.getElementById('months-error').innerText = "No monthly sales data found.";
                document.getElementById('months-error').style.display = '';
            } else {
                renderTopMonths(data.topMonths);
            }
        })
        .catch(err => {
            document.getElementById('analysis-loading').style.display = 'none';
            document.getElementById('products-loading').style.display = 'none';
            document.getElementById('months-loading').style.display = 'none';
            document.getElementById('analysis-error').innerText = "Failed to load analysis data.";
            document.getElementById('analysis-error').style.display = '';
            document.getElementById('products-error').innerText = "Failed to load product data.";
            document.getElementById('products-error').style.display = '';
            document.getElementById('months-error').innerText = "Failed to load monthly data.";
            document.getElementById('months-error').style.display = '';
        });
}

function renderChart(bars, type) {
    const ctx = document.getElementById('salesChart').getContext('2d');
    if (window.salesChartInstance) window.salesChartInstance.destroy();

    let labels = [], sales = [], quantities = [];
    if (type === "year") {
        labels = bars.map(b => String(b.label));
        sales = bars.map(b => Number(b.totalSale));
        quantities = bars.map(b => b.quantitySold);
    } else if (type === "month") {
        labels = [];
        sales = [];
        quantities = [];
        for (let m = 1; m <= 12; ++m) {
            let bar = bars.find(b => Number(b.label) === m);
            labels.push(monthName(m));
            sales.push(bar ? Number(bar.totalSale) : 0);
            quantities.push(bar ? bar.quantitySold : 0);
        }
    } else {
        labels = bars.map(b => b.label);
        sales = bars.map(b => Number(b.totalSale));
        quantities = bars.map(b => b.quantitySold);
    }

    window.salesChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sale (in Rupees)',
                data: sales,
                backgroundColor: '#1e90ff',
                hoverBackgroundColor: '#0ff1ce'
            }]
        },
        options: {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let sale = formatRupees(context.parsed.y);
                            let qty = quantities[context.dataIndex];
                            return `Sale (in Rupees): ${sale}${qty ? `, Quantity: ${qty}` : ""}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: val => formatRupees(val) }
                }
            }
        }
    });
}

function renderTopProducts(products) {
    let tbl = document.getElementById('topProducts');
    tbl.innerHTML = `<tr>
        <th>Product Name</th><th>Quantity Sold</th><th>Rate/Piece (in Rupees)</th><th>Sale (in Rupees)</th>
    </tr>`;
    products.forEach(p => {
        tbl.innerHTML += `<tr>
            <td>${p.name}</td>
            <td>${p.quantity}</td>
            <td>${formatRupees(p.rate)}</td>
            <td>${formatRupees(p.total)}</td>
        </tr>`;
    });
}

function renderTopMonths(months) {
    let tbl = document.getElementById('topMonths');
    tbl.innerHTML = `<tr>
        <th>Month</th><th>Sale (in Rupees)</th><th>Most Sold Item</th>
    </tr>`;
    months.forEach(m => {
        tbl.innerHTML += `<tr>
            <td>${monthName(Number(m.Month))}</td>
            <td>${formatRupees(m.sales)}</td>
            <td>${m.mostSold}</td>
        </tr>`;
    });
}

// --- Detail Analysis Page (summary only) ---
function runDetailAnalysis() {
    const type = document.getElementById('detailType').value;
    document.getElementById('detail-loading').style.display = '';
    document.getElementById('detail-error').style.display = 'none';
    document.getElementById('detailResult').innerHTML = '';
    fetch('api/' + (type === 'classification' ? 'classification.php' : 'clustering.php'))
        .then(res => {
            if (!res.ok) throw new Error('API error: ' + res.status);
            return res.json();
        })
        .then(data => {
            document.getElementById('detail-loading').style.display = 'none';
            if (type === 'clustering' && data.cluster_summary) {
                let html = "<h3>Clustering Result (K-Means)</h3>";
                html += `<div>${data.info}</div>`;
                html += "<table><tr><th>Cluster</th><th>Centroid Quantity</th><th>Centroid Price</th><th>Records in Cluster</th><th>Sample SaleIDs</th></tr>";
                data.cluster_summary.forEach(c=>{
                    html += `<tr>
                        <td>${c.Cluster}</td>
                        <td>${formatRupees(c.Centroid.Quantity)}</td>
                        <td>${formatRupees(c.Centroid.Price)}</td>
                        <td>${c.Count}</td>
                        <td>${c.SampleSaleIDs.join(', ')}</td>
                    </tr>`;
                });
                html += "</table>";
                document.getElementById('detailResult').innerHTML = html;
            } else if (type === 'classification' && data.accuracy !== undefined) {
                let html = `<h3>Classification Result (Rule-based)</h3>
                    <div>Rule: ${data.rule}</div>
                    <div>Accuracy: ${data.accuracy}% (${data.correct} correct, ${data.wrong} wrong)</div>`;
                if (data.misclassified && data.misclassified.length > 0) {
                    html += "<h4>Misclassified Examples (up to 5)</h4>";
                    html += "<table><tr><th>SaleID</th><th>Quantity</th><th>Price</th><th>Actual</th><th>Predicted</th></tr>";
                    data.misclassified.forEach(c=>{
                        html += `<tr><td>${c.SaleID}</td><td>${c.Quantity}</td><td>${c.Price}</td><td>${c.Actual}</td><td>${c.Predicted}</td></tr>`;
                    });
                    html += "</table>";
                }
                document.getElementById('detailResult').innerHTML = html;
            } else {
                document.getElementById('detailResult').innerHTML = "<pre>" + JSON.stringify(data, null, 2) + "</pre>";
            }
        })
        .catch(err => {
            document.getElementById('detail-loading').style.display = 'none';
            document.getElementById('detail-error').innerText = "Failed to load detail analysis. " + err;
            document.getElementById('detail-error').style.display = '';
        });
}