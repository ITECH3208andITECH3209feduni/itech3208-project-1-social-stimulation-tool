import { useEffect, useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import {
    MdHome,
    MdVideoLibrary,
    MdPlayLesson,
    MdFileCopy,
    MdSupervisedUserCircle,
    MdContactPage,
} from "react-icons/md";
import { Box, Flex, Text, VStack } from "@chakra-ui/react";

const tabs = [
    {
        label: "Dashboard",
        icon: <MdHome />,
        path: "/admin/dashboard",
    },
    {
        label: "Tutorial",
        icon: <MdPlayLesson />,
        path: "/admin/tutorial",
    },
    {
        label: "Videos",
        icon: <MdVideoLibrary />,
        path: "/admin/upload-video",
    },
    // {
    //     label: "Articles",
    //     icon: <MdFileCopy />,
    //     path: "/admin/article",
    // },
    {
        label: "Users",
        icon: <MdSupervisedUserCircle />,
        path: "/admin/users",
    },
    {
        label: "Contacts",
        icon: <MdContactPage />,
        path: "/admin/contact",
    },
];

function Sidebar() {
    const location = useLocation();
    const navigate = useNavigate();
    const [selectedPath, setSelectedPath] = useState("/home");

    useEffect(() => {
        setSelectedPath(location.pathname);
    }, [location.pathname]);

    const renderTab = (tab, isChild = false) => {
        const isSelected = selectedPath === tab.path;
        return (
            <VStack key={tab.label} align="start" spacing={0} pl={isChild ? 6 : 2} w="full">
                <Flex
                    align="center"
                    w="full"
                    py={2}
                    px={2}
                    borderRadius="md"
                    _hover={{
                        cursor: "pointer",
                        bg: isSelected ? "" : "brand.500",
                    }}
                    bg={isSelected ? "brand.50" : "transparent"}
                    color={isSelected ? "brand.500" : "white"}
                    onClick={() => handleTabClick(tab.path)}
                >
                    {tab.icon && <Box mr={2}>{tab.icon}</Box>}
                    <Text>{tab.label}</Text>
                </Flex>
                {tab.children?.map((child) => renderTab(child, true))}
            </VStack>
        );
    };

    const handleTabClick = (path) => {
        setSelectedPath(path);
        navigate(path);
    };

    return (
        <VStack align="start" spacing={1} flex="1">
            {tabs.map((tab) => renderTab(tab))}
        </VStack>
    );
}

export default Sidebar;
