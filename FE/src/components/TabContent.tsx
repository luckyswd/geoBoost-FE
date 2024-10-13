import React from 'react';
import Dashboard from "./Tabs/Dashboard";
import Settings from "./Tabs/Settings";

interface TabContentProps {
    selectedTab: number;
}

function TabContent({ selectedTab }: TabContentProps) {
    const contentForTab = [
        <div key="dashboard"><Dashboard/></div>,
        <div key="tab2">Content for Tab 2</div>,
        <div key="settings"><Settings/></div>,
    ];

    return (
        <>
            {contentForTab[selectedTab]}
        </>
    );
}

export default TabContent;
