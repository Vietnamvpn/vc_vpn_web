<?php
/**
 * Trang quản trị hệ thống (Admin Dashboard View)[cite: 1]
 */
use VcApp\VcHelpers\Security;
?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2 text-gray-800">Trang Quản Trị Hệ Thống (Admin Dashboard)</h1>
            <p class="text-muted">Chào mừng trở lại, <?php echo Security::e($_SESSION['admin_username'] ?? 'Admin'); ?>!</p>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng Số Người Dùng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo Security::e($totalUsers ?? 0); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Gói Cước Hoạt Động</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo Security::e($totalSubscriptions ?? 0); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Node VPN Trực Tuyến</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo Security::e($activeNodes ?? 0); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-server fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Doanh Thu Tháng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo Security::e($monthlyRevenue ?? '0 đ'); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng nhật ký hoạt động gần đây -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Nhật Ký Hoạt Động Gần Đây</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hành động</th>
                            <th>Chi tiết</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentLogs)): ?>
                            <?php foreach ($recentLogs as $log): ?>
                                <tr>
                                    <td><?php echo Security::e($log['id']); ?></td>
                                    <td><?php echo Security::e($log['action']); ?></td>
                                    <td><?php echo Security::e($log['details']); ?></td>
                                    <td><?php echo Security::e($log['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Chưa có nhật ký hoạt động nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>