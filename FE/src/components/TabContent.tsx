import React from 'react';
import Dashboard from "./Tabs/Dashboard";
import Settings from "./Tabs/Settings";
import {Holidays} from "./Tabs/Holidays";

interface TabContentProps {
    selectedTab: number;
}

function TabContent({ selectedTab }: TabContentProps) {
    const contentForTab = [
        <div key="dashboard"><Dashboard/></div>,
        <div key="holidays"><Holidays/></div>,
        <div key="settings"><Settings/></div>,
    ];

    return (
        <>
            {contentForTab[selectedTab]}
        </>
    );
}

export default TabContent;
