import { Link, Outlet } from "react-router-dom";
import { MdKeyboardDoubleArrowLeft } from "react-icons/md";
import { Box, Flex, Image } from "@chakra-ui/react";
import { accountImage } from "@/assets";

function AccountLayout() {
  return (
    <Flex h="100vh">
      
      {/* Left — image + icon */}
      <Box position="relative" flex="1">
        <Image src={accountImage} alt="background" w="100%" h="100vh" objectFit="fit" />
        
        {/* Back icon on the top left*/}
        <Box position="absolute" top="20px" left="20px">
          <Link to="/">
            <MdKeyboardDoubleArrowLeft size={32} color="white" />
          </Link>
        </Box>
      </Box>

      {/* Right — form */}
      <Box w="400px" p="60px" bg="white">
        <Outlet />
      </Box>

    </Flex>
  );
}

export default AccountLayout;