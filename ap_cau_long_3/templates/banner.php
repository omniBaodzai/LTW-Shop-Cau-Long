
<section class="slideshow-container">
  <?php
  include_once __DIR__ . '/../connect.php';
  
  $sql = "SELECT image, title, description FROM banner";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    while ($product = $result->fetch_assoc()) {
      ?>
      <div class="slide">
        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" />
        <h2><?php echo htmlspecialchars($product['title']); ?></h2>
        <p><?php echo htmlspecialchars($product['description']); ?></p>
      </div>
      <?php
    }
  } else {
    echo "<p>Chưa có banner nào để hiển thị.</p>";
  }
  ?>
</section>
