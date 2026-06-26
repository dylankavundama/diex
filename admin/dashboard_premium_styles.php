<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card-premium {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
    animation: fadeInUp 0.6s ease;
    position: relative;
    overflow: hidden;
}

.stat-card-premium:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.stat-card-premium::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
}

.stat-card-premium:hover::before {
    left: 100%;
}

.stat-card-premium .stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.stat-card-premium .stat-icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.stat-card-premium.purple .stat-icon-wrapper {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-card-premium.green .stat-icon-wrapper {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-card-premium.orange .stat-icon-wrapper {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-card-premium.blue .stat-icon-wrapper {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-card-premium.yellow .stat-icon-wrapper {
    background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%);
}

.stat-card-premium.red .stat-icon-wrapper {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
}

.stat-card-premium .stat-info h3 {
    font-size: 0.85rem;
    color: #7f8c8d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
}

.stat-card-premium .stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0.5rem 0;
}

.stat-card-premium .stat-change {
    font-size: 0.8rem;
    color: #95a5a6;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.content-card-premium {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
    margin-bottom: 2rem;
    animation: fadeInUp 0.6s ease;
}

.content-card-premium .content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f0f0;
    margin-bottom: 1.5rem;
}

.content-card-premium .content-header h2 {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.content-card-premium .content-header h2 i {
    color: #667eea;
}

.content-card-premium .content-header a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.content-card-premium .content-header a:hover {
    color: #764ba2;
}

.table-premium {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.table-premium thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.table-premium th {
    padding: 0.9rem 1rem;
    text-align: left;
    color: white;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-premium th:first-child {
    border-top-left-radius: 10px;
}

.table-premium th:last-child {
    border-top-right-radius: 10px;
}

.table-premium tbody tr {
    transition: all 0.3s ease;
}

.table-premium tbody tr:hover {
    background: #f8f9fa;
}

.table-premium td {
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
    color: #2c3e50;
}

.badge-premium {
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
}

.badge-premium.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.badge-premium.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.badge-premium.info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.btn-premium {
    padding: 0.5rem 1.2rem;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-premium.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-premium.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-premium.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.btn-premium.warning {
    background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%);
    color: white;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.quick-action-btn {
    text-align: center;
    padding: 1.5rem 1rem;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.8rem;
}

.quick-action-btn i {
    font-size: 2rem;
}

@media (max-width: 1024px) {
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .content-grid-premium {
        grid-template-columns: 1fr !important;
        gap: 1rem;
    }

    .stat-card-premium {
        padding: 1.2rem;
    }

    .stat-card-premium .stat-value {
        font-size: 1.5rem;
    }

    .content-card-premium {
        padding: 1rem;
    }

    .quick-actions-grid {
        grid-template-columns: 1fr 1fr;
    }

    /* Responsive Premium Table */
    .table-premium thead {
        display: none;
    }

    .table-premium, .table-premium tbody, .table-premium tr, .table-premium td {
        display: block;
        width: 100%;
    }

    .table-premium tr {
        margin-bottom: 1.5rem;
        border: 1px solid #f0f0f0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .table-premium td {
        text-align: right;
        padding: 0.8rem 1rem;
        position: relative;
        border-bottom: 1px solid #f9f9f9;
        min-height: 2.5rem;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .table-premium td:last-child {
        border-bottom: none;
        background: #fcfcfc;
    }

    .table-premium td::before {
        content: attr(data-label);
        position: absolute;
        left: 1rem;
        font-weight: 600;
        text-align: left;
        color: #7f8c8d;
        font-size: 0.8rem;
        text-transform: uppercase;
    }
}

@media (max-width: 480px) {
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
}
</style>
