import Image from "next/image"
import Link from "next/link"
import { Navigation } from "@/components/navigation"
import { Footer } from "@/components/footer"
import { Button } from "@/components/ui/button"
import { ArrowLeft, ArrowRight, Users, Clock, Sun, Leaf, Umbrella } from "lucide-react"

const tables = [
  { id: 1, name: "Garden View", seats: 2, description: "Intimate table beside the rose garden" },
  { id: 2, name: "Fountain Side", seats: 4, description: "Prime location near the centerpiece fountain" },
  { id: 3, name: "Pergola", seats: 6, description: "Covered seating under our wisteria-draped pergola" },
  { id: 4, name: "Corner Alcove", seats: 4, description: "Secluded spot perfect for private conversations" },
  { id: 5, name: "Olive Grove", seats: 8, description: "Our largest outdoor table, ideal for gatherings" },
]

export default function PatioPage() {
  return (
    <main className="min-h-screen">
      <Navigation />
      
      {/* Hero */}
      <section className="relative h-[60vh] min-h-[400px] flex items-end">
        <Image
          src="/images/zone-patio.jpg"
          alt="The Patio at Eudaimonia"
          fill
          className="object-cover"
          priority
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent" />
        <div className="relative z-10 container mx-auto px-4 pb-12">
          <Link href="/dining-zones" className="inline-flex items-center text-white/80 hover:text-white text-sm mb-4 transition-colors">
            <ArrowLeft className="h-4 w-4 mr-2" />
            All Dining Zones
          </Link>
          <p className="text-white/80 text-sm uppercase tracking-[0.2em] mb-2 font-medium">
            Al Fresco Elegance
          </p>
          <h1 className="font-serif text-4xl md:text-5xl lg:text-6xl text-white">
            The Patio
          </h1>
        </div>
      </section>

      {/* Overview */}
      <section className="py-16 md:py-24 bg-background">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div className="lg:col-span-2 space-y-6">
              <h2 className="font-serif text-2xl md:text-3xl text-foreground">
                Dining Under the Stars
              </h2>
              <div className="space-y-4 text-muted-foreground leading-relaxed">
                <p>
                  The Patio at Eudaimonia is a sanctuary of natural beauty and culinary excellence. Our garden courtyard has been meticulously designed to transport guests to a Mediterranean oasis, where every detail—from the hand-selected flora to the ambient lighting—creates an atmosphere of enchantment.
                </p>
                <p>
                  During warm evenings, enjoy your meal al fresco surrounded by the gentle fragrance of jasmine and lavender. Our retractable canopy ensures comfort in any weather, while subtle heating elements extend the season well into autumn.
                </p>
                <p>
                  The Patio menu celebrates seasonal ingredients at their peak, with lighter preparations that complement the outdoor setting without sacrificing the sophistication that defines Eudaimonia.
                </p>
              </div>
            </div>

            <div className="bg-muted p-8 rounded-sm space-y-6">
              <h3 className="font-serif text-xl text-foreground">Details</h3>
              <div className="space-y-4">
                <div className="flex items-start gap-3">
                  <Users className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Capacity</p>
                    <p className="text-sm text-muted-foreground">40 guests maximum</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Clock className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Hours</p>
                    <p className="text-sm text-muted-foreground">5:00 PM - 10:00 PM</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Sun className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Season</p>
                    <p className="text-sm text-muted-foreground">April through October</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Umbrella className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Weather</p>
                    <p className="text-sm text-muted-foreground">Covered with retractable canopy</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Leaf className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Ambiance</p>
                    <p className="text-sm text-muted-foreground">Garden setting with ambient lighting</p>
                  </div>
                </div>
              </div>
              <Link href="/book" className="block">
                <Button className="w-full bg-primary text-primary-foreground hover:bg-primary/90">
                  Reserve a Table
                  <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Tables */}
      <section className="py-16 md:py-24 bg-muted">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto text-center mb-12">
            <p className="text-primary text-sm uppercase tracking-[0.2em] mb-4 font-medium">
              Seating Options
            </p>
            <h2 className="font-serif text-2xl md:text-3xl text-foreground">
              Choose Your Perfect Table
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            {tables.map((table) => (
              <div key={table.id} className="bg-card p-6 rounded-sm border border-border">
                <div className="flex items-center justify-between mb-3">
                  <h3 className="font-serif text-lg text-foreground">{table.name}</h3>
                  <span className="text-sm text-muted-foreground">{table.seats} seats</span>
                </div>
                <p className="text-sm text-muted-foreground">{table.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-16 md:py-24 bg-primary text-primary-foreground">
        <div className="container mx-auto px-4 text-center">
          <h2 className="font-serif text-3xl md:text-4xl mb-6">
            Reserve The Patio
          </h2>
          <p className="text-primary-foreground/80 text-lg max-w-xl mx-auto mb-10">
            Book your table in our enchanting garden courtyard for an unforgettable outdoor dining experience.
          </p>
          <Link href="/book">
            <Button size="lg" variant="outline" className="border-primary-foreground text-primary-foreground hover:bg-primary-foreground hover:text-primary px-8">
              Make a Reservation
              <ArrowRight className="ml-2 h-4 w-4" />
            </Button>
          </Link>
        </div>
      </section>

      <Footer />
    </main>
  )
}
