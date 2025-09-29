<?php
include_once "db_config.php";

class Product {
    public $id;
    public $name;
    public $price;
    public $manufacturer_id;

    public function __construct($id = null, $name = null, $price = null, $manufacturer_id = null) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->manufacturer_id = $manufacturer_id;
    }

    // Insert new product
    public function save() {
        global $db;
        $stmt = $db->prepare("INSERT INTO product (name, price, manufacturer_id) VALUES (?, ?, ?)");
        if (!$stmt) return "Error: " . $db->error;
        $stmt->bind_param("sdi", $this->name, $this->price, $this->manufacturer_id);

        if ($stmt->execute()) {
            $stmt->close();
            return "✅ Product saved successfully";
        }
        return "❌ Failed to save product: " . $stmt->error;
    }

    // Show all products
    public static function showProduct() {
        global $db;
        $data = $db->query("SELECT * FROM product ORDER BY id DESC");
        $html = "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse:collapse; margin-top:10px;'>";
        $html .= "<tr style='background:#f2f2f2;'>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Manufacturer ID</th>
                    <th>Actions</th>
                  </tr>";
        while ($row = $data->fetch_object()) {
            $html .= "<tr>
                        <td>{$row->id}</td>
                        <td>{$row->name}</td>
                        <td>{$row->price}</td>
                        <td>{$row->manufacturer_id}</td>
                        <td>
                            <a href='newproduct.php?edit={$row->id}' style='padding:5px 10px; background: teal; color:#fff; text-decoration:none;'>Edit</a>
                            <a href='newproduct.php?delete={$row->id}' style='padding:5px 10px; background: crimson; color:#fff; text-decoration:none;' onclick=\"return confirm('Are you sure?');\">Delete</a>
                        </td>
                      </tr>";
        }
        $html .= "</table>";
        return $html;
    }

    // Delete product
    public static function delete($id) {
        global $db;
        $stmt = $db->prepare("DELETE FROM product WHERE id=?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Update product
    public function update() {
        global $db;
        $stmt = $db->prepare("UPDATE product SET name=?, price=?, manufacturer_id=? WHERE id=?");
        if (!$stmt) return false;
        $stmt->bind_param("sdii", $this->name, $this->price, $this->manufacturer_id, $this->id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Fetch single product
    public static function search($id) {
        global $db;
        $stmt = $db->prepare("SELECT * FROM product WHERE id=?");
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }
}
?>
