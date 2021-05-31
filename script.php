<?php
    include "db.php";

    //read the json file contents
    $data = file_get_contents('products.json');

    //convert json object to php associative array
    $products = json_decode($data);
    
    //insertion of values 
    foreach ($products as $product) {
        //product table
        $stmt = $conn->prepare('insert into product(ref, name, type, price, shipping, description, manufacturer, image) values(:ref, :name, :type, :price, :shipping, :description, :manufacturer, :image)');
        $stmt->bindValue('ref', $product->ref);
        $stmt->bindValue('name', $product->name);
        $stmt->bindValue('type', $product->type);
        $stmt->bindValue('price', $product->price);
        $stmt->bindValue('shipping', $product->shipping);
        $stmt->bindValue('description', $product->description);
        $stmt->bindValue('manufacturer', $product->manufacturer);
        $stmt->bindValue('image', $product->image);
        $stmt->execute();
        foreach($product->category as $category){
            //category table
            $stmt = $conn->prepare('insert ignore into category(id, name) values(:id, :name)');
            $stmt->bindValue('id', $category->id);
            $stmt->bindValue('name', $category->name);
            $stmt->execute();
            //category_product table
            $stmt = $conn->prepare('insert into category_product(category, product) values(:category, :product)');
            $stmt->bindValue('category', $category->id);
            $stmt->bindValue('product', $product->ref);
            $stmt->execute();
        }
    }
    //user with id -1 to present unregisted client 
    $stmt = $conn->prepare('insert into client(id) values(-1)');
    $stmt->execute();
    $stmt = $conn->prepare('INSERT INTO `order`(`client`) VALUES (-1)');
    $stmt->execute();

?>