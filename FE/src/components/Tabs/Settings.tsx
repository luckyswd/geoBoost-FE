import { BlockStack } from '@shopify/polaris';
import PopupActivated from "./Settings/PopupActivated";

export default function Settings() {
    return (
        <BlockStack gap="300">
            <PopupActivated/>
        </BlockStack>
    );
}
