<?xml version="1.0" encoding="UTF-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
    <Header>
        <DocumentVersion>1.01</DocumentVersion>
        <MerchantIdentifier>A39USQT4A3RBVR</MerchantIdentifier>
    </Header>
    <MessageType>Price</MessageType>
    @foreach($products as $product)
    <Message>
        <MessageID>{{$loop->index+1}}</MessageID>
        <Price>
            <SKU>{{$product->sku}}</SKU>
            <StandardPrice currency="EUR">{{$product->price}}</StandardPrice>
        </Price>
    </Message>
    @endforeach
</AmazonEnvelope>