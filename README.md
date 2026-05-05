# 📈 Retail Pulse

> A data-driven retail analytics dashboard powered by ML classification & customer clustering — built with zero external JS dependencies.

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Glossary/HTML5)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-323330?style=for-the-badge&logo=javascript&logoColor=F7DF1E)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)

---

## 🚀 Live Demo

> 📌 **Run locally using XAMPP** — see [Setup Instructions](#️-local-setup-instructions) below.

---

## 📸 Screenshots

| Dashboard View | Data Analytics |
|---|---|
| ![Dashboard](assets/images/image1.png) | ![Analytics](assets/images/image2.png) |

| Classification | Clustering |
|---|---|
| ![Classification](assets/images/image3.png) | ![Clustering](assets/images/image4.png) |

<details>
<summary>📂 View More Screenshots</summary>

| Additional View 1 | Additional View 2 |
|---|---|
| ![Extra1](assets/images/image5.png) | ![Extra2](assets/images/image6.png) |

</details>

---

## ✨ Features

- 🧠 **Smart Classification** — Rule-based ML algorithm predicts customer types based on purchasing habits.
- 🎯 **Customer Clustering** — Groups retail behaviors dynamically for targeted business insights.
- 📊 **Real-time Analytics** — Interactive dashboard displaying live sales trends and KPIs.
- 🚀 **Lightweight PHP API** — Zero-framework backend serving clean JSON directly to the frontend.
- 🗃️ **MySQL Integration** — Structured data tracking Sales ID, Quantity, Price, and Customer Types.

---

## 🛠️ Tech Stack & Architecture

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Backend | PHP (Procedural, no framework) |
| Database | MySQL |
| Dev Server | XAMPP (Apache + MySQL) |

```
retailpulse/
├── api/                  # PHP endpoints (data + ML logic)
│   └── dbconnect.php     # DB connection config
├── assets/
│   └── images/           # Screenshots & UI assets
├── index.html            # Main dashboard
├── analysis.html         # Analytics view
├── detail_analysis.html  # Detailed drill-down view
├── main.js               # Frontend logic
└── style.css             # Styling
```

---

## ⚙️ Local Setup Instructions

1. **Clone the repo**
   ```bash
   git clone https://github.com/ritesh-0608/retailpulse.git
   ```

2. **Move to XAMPP's root directory**
   ```
   C:\xampp\htdocs\retailpulse\   (Windows)
   /Applications/XAMPP/htdocs/retailpulse/   (macOS)
   ```

3. **Start Apache & MySQL** via XAMPP Control Panel.

4. **Configure DB credentials** in `api/dbconnect.php`:
   ```php
   $host = "localhost";
   $user = "root";
   $password = "";
   $database = "retailpulse";
   ```

5. **Open in browser:**
   ```
   http://localhost/retailpulse
   ```

---

## 🧠 How the ML Works

**Classification** — A rule-based algorithm segments customers into types (e.g., Loyal, At-Risk, New) based on purchase frequency, recency, and total spend.

**Clustering** — Customers are grouped by behavioral similarity using distance-based logic, enabling targeted marketing without any external ML library.

All logic runs server-side in PHP and is served as JSON to the frontend charts.

---

## 🤝 Contributing

Contributions are welcome! Please check [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

*Engineered with ❤️ by [Ritesh](https://github.com/ritesh-0608).*
