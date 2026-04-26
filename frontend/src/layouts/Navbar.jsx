import { Flex, HStack, Box, Image } from "@chakra-ui/react"
import { Link } from "react-router-dom"
import { federationLogo } from "@/assets"

const Navbar = () => {
  return (
    <Flex 
      as="nav"
      align="center"
      justify="space-between"
      px="100px"
      h="80px"
      borderBottom="1px solid"
      borderColor="gray.200"
      bg="white"
      gap="6"
      color="black"
      fontSize={16}
    >
      <Image src={federationLogo} alt="Federation University" h="60px" />
      <HStack gap="8">
        <Link to="/">Home</Link>
        <Link to="/about">About</Link>
        <Link to="/tutorial">Tutorial</Link>
        <Link to="/contact">Contact</Link>
        <Link to="/terms">Terms & Conditions</Link>
        <Link to="/account">Account</Link>

        <Box w="9" h="9" borderRadius="full" bg="red.500" display="flex" alignItems="center" justifyContent="center" cursor="pointer">
          <span style={{ color: "white", fontSize: "12px", fontWeight: "bold" }}>AP</span>
        </Box>
      </HStack>
    </Flex>
  )
}

export default Navbar