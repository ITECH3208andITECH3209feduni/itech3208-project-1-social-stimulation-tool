import { Button } from "@chakra-ui/react";
import { LuX } from "react-icons/lu";

function ClearFilterBadge({ 
    onClick, 
    bg = "#1a1d26", 
    borderColor = "rgba(255,255,255,0.15)",
    color = "whiteAlpha.700",
    h = "40px"
}) {
    return (
        <Button
            display="flex"
            alignItems="center"
            gap={2}
            bg={bg}
            color={color}
            borderWidth="1px"
            borderColor={borderColor}
            borderRadius="md"
            onClick={onClick}
            fontWeight="normal"
            h={h}
            px={4}
        >
            <LuX size={16} />
            Clear filters
        </Button>
    );
}

export default ClearFilterBadge;
