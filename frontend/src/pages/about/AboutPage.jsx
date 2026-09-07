import { Container } from "@chakra-ui/react";
import AboutUs from "./AboutUs";
import OurMission from "./OurMission";
import OurTeam from "./OurTeam";
import Contribution from "./Contribution";
import Crowdfunding from "./Crowdfunding";
import WINNews from "./WINNews";

function AboutPage() {
    return (
        <Container spaceY={"50px"}>
            <AboutUs />
            <OurMission />
            <OurTeam />
            <Contribution />
            <WINNews/>
            <Crowdfunding />
        </Container>
    );
}

export default AboutPage;
