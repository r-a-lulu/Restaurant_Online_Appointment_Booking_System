import Image from "next/image"
import Link from "next/link"
import { Navigation } from "@/components/navigation"
import { Footer } from "@/components/footer"
import { Button } from "@/components/ui/button"
import { ArrowRight, Users, Clock, Sun, Moon, Wine } from "lucide-react"

const zones = [
  {
    id: "patio",
    name: "The Patio",
    tagline: "Al Fresco Elegance",
    description: "Nestled within our lush garden courtyard, The Patio offers an enchanting outdoor dining experience. Surrounded by climbing vines and fragrant blooms, guests enjoy seasonal cuisine under the open sky or our retractable canopy.",
    image: "/images/zone-patio.jpg",
    features: [
      { icon: Users, label: "Capacity: 40 guests" },
      { icon: Sun, label: "Weather-protected" },
      { icon: Clock, label: "Available April - October" },
    ],
    href: "/dining-zones/patio",
  },
  {
    id: "dining-room",
    name: "Main Dining Room",
    tagline: "Timeless Sophistication",
    description: "Our signature dining space embodies the essence of Eudaimonia. Crystal chandeliers cast a warm glow over intimate tables, while our open kitchen allows guests to witness culinary artistry in action. This is where memories are made.",
    image: "/images/zone-dining.jpg",
    features: [
      { icon: Users, label: "Capacity: 80 guests" },
      { icon: Moon, label: "Dinner service nightly" },
      { icon: Clock, label: "Available year-round" },
    ],
    href: "/dining-zones/dining-room",
  },
  {
    id: "bar",
    name: "The Bar",
    tagline: "Intimate Indulgence",
    description: "A sophisticated retreat for those seeking an elevated cocktail experience. Our bar features a curated selection of rare spirits, craft cocktails, and an expertly chosen wine list, complemented by an inventive small plates menu.",
    image: "/images/zone-bar.jpg",
    features: [
      { icon: Users, label: "Capacity: 24 guests" },
      { icon: Wine, label: "Full cocktail service" },
      { icon: Clock, label: "Opens at 4pm daily" },
    ],
    href: "/dining-zones/bar",
  },
]

export default function DiningZonesPage() {
  return (
    <main className="min-h-screen">
      <Navigation />
      
      {/* Hero */}
      <section className="pt-32 pb-16 md:pt-40 md:pb-24 bg-background">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto text-center">
            <p className="text-primary text-sm uppercase tracking-[0.2em] mb-4 font-medium">
              Spaces
            </p>
            <h1 className="font-serif text-4xl md:text-5xl lg:text-6xl text-foreground mb-6 text-balance leading-tight">
              Dining Zones
            </h1>
            <p className="text-muted-foreground text-lg leading-relaxed">
              Three distinct atmospheres, each designed to create the perfect setting for your dining experience.
            </p>
          </div>
        </div>
      </section>

      {/* Zones */}
      <section className="pb-16 md:pb-24 bg-background">
        <div className="container mx-auto px-4">
          <div className="space-y-24">
            {zones.map((zone, index) => (
              <div
                key={zone.id}
                className={`grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center ${
                  index % 2 === 1 ? "lg:flex-row-reverse" : ""
                }`}
              >
                <div className={`relative aspect-[4/3] rounded-sm overflow-hidden ${index % 2 === 1 ? "lg:order-2" : ""}`}>
                  <Image
                    src={zone.image}
                    alt={zone.name}
                    fill
                    loading={index === 0 ? "eager" : "lazy"}
                    className="object-cover"
                  />
                </div>
                <div className={`space-y-6 ${index % 2 === 1 ? "lg:order-1" : ""}`}>
                  <div>
                    <p className="text-primary text-sm uppercase tracking-[0.2em] font-medium mb-2">
                      {zone.tagline}
                    </p>
                    <h2 className="font-serif text-3xl md:text-4xl text-foreground">
                      {zone.name}
                    </h2>
                  </div>
                  <p className="text-muted-foreground leading-relaxed">
                    {zone.description}
                  </p>
                  <div className="flex flex-wrap gap-4">
                    {zone.features.map((feature) => (
                      <div
                        key={feature.label}
                        className="flex items-center gap-2 text-sm text-muted-foreground"
                      >
                        <feature.icon className="h-4 w-4 text-primary" />
                        <span>{feature.label}</span>
                      </div>
                    ))}
                  </div>
                  <Link href={zone.href}>
                    <Button variant="outline" className="mt-2">
                      View Details
                      <ArrowRight className="ml-2 h-4 w-4" />
                    </Button>
                  </Link>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Private Events CTA */}
      <section className="py-16 md:py-24 bg-muted">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto text-center">
            <p className="text-primary text-sm uppercase tracking-[0.2em] mb-4 font-medium">
              Private Events
            </p>
            <h2 className="font-serif text-3xl md:text-4xl text-foreground mb-6">
              Host Your Special Occasion
            </h2>
            <p className="text-muted-foreground text-lg leading-relaxed mb-8">
              Each of our dining zones can be reserved for private events. From intimate dinners to corporate gatherings, our team will create a bespoke experience tailored to your needs.
            </p>
            <Link href="/book">
              <Button size="lg" className="bg-primary text-primary-foreground hover:bg-primary/90">
                Inquire About Events
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </Link>
          </div>
        </div>
      </section>

      <Footer />
    </main>
  )
}
