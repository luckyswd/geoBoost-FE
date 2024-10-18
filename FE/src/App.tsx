import {AppProvider} from "@shopify/polaris";
import enTranslations from "@shopify/polaris/locales/en.json";
import Tabs from "./components/Tabs";
import {useAppBridge} from "@shopify/app-bridge-react";
import {GlobalSettingsProvider} from "./Provaiders/GlobalSettingsProvider";

export default function App() {
    useAppBridge();

    return (
        <AppProvider i18n={enTranslations}>
            <GlobalSettingsProvider>
                <Tabs/>
            </GlobalSettingsProvider>
        </AppProvider>
    );
}
