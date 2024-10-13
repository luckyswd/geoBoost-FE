import {Text, List, BlockStack, Card} from '@shopify/polaris';

export default function Dashboard() {
    return (
        <Card>
            <BlockStack gap="500">
                <Text variant="headingLg" as="h2">Welcome on board!</Text>
                <div>
                    <BlockStack gap="200">
                        <Text variant="headingMd" as="h3" >Main features summary:</Text>
                        <List type="bullet" gap="loose">
                            <List.Item>Analyze all products in your store to find the best matches for upcoming holidays</List.Item>
                            <List.Item>Filter and categorize your products based on specific holiday criteria</List.Item>
                            <List.Item>Get insights on product performance during holiday seasons</List.Item>
                            <List.Item>Receive recommendations to optimize listings for increased holiday sales</List.Item>
                        </List>
                    </BlockStack>
                </div>
            </BlockStack>
        </Card>
    );
}
