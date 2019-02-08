<?xml version="1.0"?>
<AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <Header>
        <DocumentVersion>1.01</DocumentVersion>
        <MerchantIdentifier>A39USQT4A3RBVR</MerchantIdentifier>
    </Header>
    <MessageType>Product</MessageType>
    <PurgeAndReplace>false</PurgeAndReplace>
    @foreach($products as $product)
    <Message>
        <MessageID>{{$loop->index+1}}</MessageID>
        <OperationType>Update</OperationType>
        <Product>
            <SKU>{{$product->sku}}</SKU>
            <StandardProductID>
                <Type>EAN</Type>
                <Value>{{$product->barcode}}</Value>
            </StandardProductID>
            <Condition>
                <ConditionType>New</ConditionType>
            </Condition>
        </Product>
    </Message>
    @endforeach
</AmazonEnvelope>