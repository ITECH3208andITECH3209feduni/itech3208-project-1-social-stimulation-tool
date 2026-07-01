import { Avatar } from "@chakra-ui/react";
import PropTypes from "prop-types";

function UserAvatar({ src, name, size = "md", ...props }) {
    return (
        <Avatar.Root
            size={size}
            borderRadius="full"
            overflow="hidden"
            display="flex"
            alignItems="center"
            justifyContent="center"
            bg="brand.500"
            {...props}
        >
            <Avatar.Image src={src} w="100%" h="100%" objectFit="cover" />
            <Avatar.Fallback>{name?.charAt(0).toUpperCase()}</Avatar.Fallback>
        </Avatar.Root>
    );
}

UserAvatar.propTypes = {
    src: PropTypes.string,
    name: PropTypes.string,
    size: PropTypes.string,
};

export default UserAvatar;
