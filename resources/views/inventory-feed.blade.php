<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
    <Header>
        <DocumentVersion>1.01</DocumentVersion>
        <MerchantIdentifier>A39USQT4A3RBVR</MerchantIdentifier>
    </Header>
    <MessageType>Inventory</MessageType>
    @foreach($products as $product)
    <Message>
        <MessageID>{{$loop->index+1}}</MessageID>
        <OperationType>Update</OperationType>
        <Inventory>
            <SKU>{{ $product->sku }}</SKU>
            <Quantity>{{ $product->inventory }}</Quantity>
        </Inventory>
    </Message>
    @endforeach
</AmazonEnvelope>