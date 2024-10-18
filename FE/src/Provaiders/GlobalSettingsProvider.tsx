import {createContext, useContext, useState} from "react";
import {ApiV1Response} from "../type/global";
import {Settings} from "../components/Tabs/type/SettingType";

const GlobalSettingsContext = createContext<{
    globalSettings: ApiV1Response<Settings> | undefined;
    setGlobalSettings: (settings: ApiV1Response<Settings>) => void;
} | undefined>(undefined);

export const GlobalSettingsProvider = ({children}: { children: React.ReactNode }) => {
    const [globalSettings, setGlobalSettings] = useState<ApiV1Response<Settings>>();

    return (
        <GlobalSettingsContext.Provider value={{globalSettings, setGlobalSettings}}>
            {children}
        </GlobalSettingsContext.Provider>
    );
};

export const useGlobalSettingSettings = () => {
    const context = useContext(GlobalSettingsContext);

    if (!context) {
        throw new Error("useSettings must be used within a SettingsProvider");
    }

    return context;
};
