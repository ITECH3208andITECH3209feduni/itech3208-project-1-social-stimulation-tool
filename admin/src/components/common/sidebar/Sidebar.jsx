import { useEffect, useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import { useQueryClient } from "@tanstack/react-query";
import {
    MdHome,
    MdVideoLibrary,
    MdPlayLesson,
    MdFileCopy,
    MdSupervisedUserCircle,
    MdContactPage,
    MdCategory,
} from "react-icons/md";
import { Box, Flex, Text, VStack } from "@chakra-ui/react";
import { videoQueries } from "@/api/videos/video.queries";

// Paths that support prefetch on hover, mapped to a prefetch function
const PREFETCH_MAP = {
    "/admin/video-management": (queryClient) =>
        queryClient.prefetchQuery(
            videoQueries.list({ page: 1, limit: 12, status: "", categoryId: "" }),
        ),
};

const tabs = [
    {
        label: "Dashboard",
        icon: <MdHome />,
        path: "/admin/dashboard",
    },
    {
        label: "Video Management",
        icon: <MdPlayLesson />,
        path: "/admin/video-management",
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
        label: "Categories",
        icon: <MdCategory />,
        path: "/admin/categories",
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
    const queryClient = useQueryClient();
    const [selectedPath, setSelectedPath] = useState("/home");

    useEffect(() => {
        setSelectedPath(location.pathname);
    }, [location.pathname]);

    const handleTabClick = (path) => {
        setSelectedPath(path);
        navigate(path);
    };

    const handleTabHover = (path) => {
        const prefetch = PREFETCH_MAP[path];
        if (prefetch) {
            prefetch(queryClient);
        }
    };

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
                    onMouseEnter={() => handleTabHover(tab.path)}
                >
                    {tab.icon && <Box mr={2}>{tab.icon}</Box>}
                    <Text>{tab.label}</Text>
                </Flex>
                {tab.children?.map((child) => renderTab(child, true))}
            </VStack>
        );
    };

    return (
        <VStack align="start" spacing={1} flex="1">
            {tabs.map((tab) => renderTab(tab))}
        </VStack>
    );
}

export default Sidebar;
