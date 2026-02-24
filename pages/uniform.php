<?php
// get_uniform.php
require_once '../config/dbconnection                                                                                                                                                                                                                                                                                        .php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $query = "
        SELECT u.*, s.supplier_name, s.contact_person, s.email, s.phone 
        FROM Uniforms u 
        LEFT JOIN supplier s ON u.supplier_id = s.id 
        WHERE u.uniform_id = ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        ?>
        <div style="display: grid; gap: 20px;">
            <div style="display: flex; gap: 20px; align-items: center;">
                <div style="background: #e2f0e6; width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-tshirt" style="font-size: 40px; color: #1a4d2e;"></i>
                </div>
                <div>
                    <h3 style="color: #1a4d2e; margin-bottom: 5px;"><?php echo htmlspecialchars($row['uniform_name']); ?></h3>
                    <span class="category-badge <?php echo strtolower($row['category']); ?>">
                        <?php echo $row['category'] == 'Male' ? '👔 Boys' : '👗 Girls'; ?>
                    </span>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div style="background: #f5fbf7; padding: 16px; border-radius: 16px;">
                    <div style="color: #4a6b57; font-size: 13px; margin-bottom: 4px;">Size</div>
                    <div style="font-weight: 600; font-size: 18px;"><?php echo htmlspecialchars($row['size']); ?></div>
                </div>
                <div style="background: #f5fbf7; padding: 16px; border-radius: 16px;">
                    <div style="color: #4a6b57; font-size: 13px; margin-bottom: 4px;">Color</div>
                    <div style="font-weight: 600; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-block; width: 16px; height: 16px; background: <?php echo strtolower($row['color']); ?>; border-radius: 4px;"></span>
                        <?php echo htmlspecialchars($row['color']); ?>
                    </div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div style="background: #f5fbf7; padding: 16px; border-radius: 16px;">
                    <div style="color: #4a6b57; font-size: 13px; margin-bottom: 4px;">Price</div>
                    <div style="font-weight: 600; font-size: 24px; color: #1a4d2e;">₱<?php echo number_format($row['price'], 2); ?></div>
                </div>
                <div style="background: #f5fbf7; padding: 16px; border-radius: 16px;">
                    <div style="color: #4a6b57; font-size: 13px; margin-bottom: 4px;">Current Stock</div>
                    <div style="font-weight: 600; font-size: 24px; color: #1a4d2e;"><?php echo $row['quantity']; ?> <span style="font-size: 14px;">pcs</span></div>
                </div>
            </div>
            
            <div style="background: #f5fbf7; padding: 16px; border-radius: 16px;">
                <div style="color: #4a6b57; font-size: 13px; margin-bottom: 4px;">Low Stock Alert Level</div>
                <div style="font-weight: 600; font-size: 18px;"><?php echo $row['low_stock_limit']; ?> pieces</div>
            </div>
            
            <?php if ($row['supplier_name']): ?>
            <div style="background: #f5fbf7; padding: 16px; border-radius: 16px;">
                <div style="color: #4a6b57; font-size: 13px; margin-bottom: 8px;">Supplier Information</div>
                <div style="font-weight: 600;"><?php echo htmlspecialchars($row['supplier_name']); ?></div>
                <?php if ($row['contact_person']): ?>
                    <div style="color: #4a6b57; font-size: 14px;">Contact: <?php echo htmlspecialchars($row['contact_person']); ?></div>
                <?php endif; ?>
                <?php if ($row['email']): ?>
                    <div style="color: #4a6b57; font-size: 14px;">Email: <?php echo htmlspecialchars($row['email']); ?></div>
                <?php endif; ?>
                <?php if ($row['phone']): ?>
                    <div style="color: #4a6b57; font-size: 14px;">Phone: <?php echo htmlspecialchars($row['phone']); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div style="color: #4a6b57; font-size: 12px; text-align: right;">
                Added: <?php echo date('F j, Y', strtotime($row['date_added'])); ?>
            </div>
        </div>
        <?php
    } else {
        echo "<p style='color: #c24f4a;'>Uniform not found.</p>";
    }
}
?>