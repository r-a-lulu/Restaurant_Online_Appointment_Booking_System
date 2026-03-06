import Image from "next/image"
import Link from "next/link"
import { Navigation } from "@/components/navigation"
import { Footer } from "@/components/footer"
import { Button } from "@/components/ui/button"
import { ArrowLeft, ArrowRight, Users, Clock, Wine, Martini, Music } from "lucide-react"

const seating = [
  { id: 1, name: "Bar Counter", seats: 8, description: "Premium seats at our mahogany bar" },
  { id: 2, name: "Lounge Booths", seats: 4, description: "Intimate leather booth seating" },
  { id: 3, name: "High Tops", seats: 4, description: "Casual elevated tables" },
  { id: 4, name: "Corner Sofa", seats: 6, description: "Relaxed seating for larger groups" },
]

const signatures = [
  { name: "The Flourish", description: "Aged rum, honey, citrus, sage", price: "$18" },
  { name: "Golden Hour", description: "Single malt, amaro, orange bitters", price: "$22" },
  { name: "Garden State", description: "Gin, elderflower, cucumber, basil", price: "$16" },
  { name: "Midnight in Paris", description: "Cognac, champagne, violet liqueur", price: "$24" },
]

export default function BarPage() {
  return (
    <main className="min-h-screen">
      <Navigation />
      
      {/* Hero */}
      <section className="relative h-[60vh] min-h-[400px] flex items-end">
        <Image
          src="/images/zone-bar.jpg"
          alt="The Bar at Eudaimonia"
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
            Intimate Indulgence
          </p>
          <h1 className="font-serif text-4xl md:text-5xl lg:text-6xl text-white">
            The Bar
          </h1>
        </div>
      </section>

      {/* Overview */}
      <section className="py-16 md:py-24 bg-background">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div className="lg:col-span-2 space-y-6">
              <h2 className="font-serif text-2xl md:text-3xl text-foreground">
                The Art of the Cocktail
              </h2>
              <div className="space-y-4 text-muted-foreground leading-relaxed">
                <p>
                  The Bar at Eudaimonia is a sanctuary for those who appreciate the finer things. Our intimate space, anchored by a stunning 30-foot mahogany bar, pays homage to the golden age of cocktail culture while embracing contemporary innovation.
                </p>
                <p>
                  Behind the bar, our expert mixologists craft each drink with precision and artistry. Our cocktail program features house originals alongside expertly executed classics, all utilizing premium spirits and house-made ingredients.
                </p>
                <p>
                  Complement your drinks with our curated menu of small plates—perfect for sharing or enjoying solo. From charcuterie boards to our signature truffle fries, each dish is designed to enhance your bar experience.
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
                    <p className="text-sm text-muted-foreground">24 guests maximum</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Clock className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Hours</p>
                    <p className="text-sm text-muted-foreground">Opens 4 PM daily</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Wine className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Selection</p>
                    <p className="text-sm text-muted-foreground">200+ spirits, craft cocktails</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Martini className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Food</p>
                    <p className="text-sm text-muted-foreground">Small plates & bar bites</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Music className="h-5 w-5 text-primary mt-0.5" />
                  <div>
                    <p className="font-medium text-foreground">Atmosphere</p>
                    <p className="text-sm text-muted-foreground">Live jazz on weekends</p>
                  </div>
                </div>
              </div>
              <Link href="/book" className="block">
                <Button className="w-full bg-primary text-primary-foreground hover:bg-primary/90">
                  Reserve a Seat
                  <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Signature Cocktails */}
      <section className="py-16 md:py-24 bg-muted">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto text-center mb-12">
            <p className="text-primary text-sm uppercase tracking-[0.2em] mb-4 font-medium">
              Signature Cocktails
            </p>
            <h2 className="font-serif text-2xl md:text-3xl text-foreground">
              House Creations
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">
            {signatures.map((drink) => (
              <div key={drink.name} className="bg-card p-6 rounded-sm border border-border flex justify-between items-start">
                <div>
                  <h3 className="font-serif text-lg text-foreground mb-1">{drink.name}</h3>
                  <p className="text-sm text-muted-foreground">{drink.description}</p>
                </div>
                <span className="text-primary font-medium">{drink.price}</span>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Seating */}
      <section className="py-16 md:py-24 bg-background">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto text-center mb-12">
            <p className="text-primary text-sm uppercase tracking-[0.2em] mb-4 font-medium">
              Seating Options
            </p>
            <h2 className="font-serif text-2xl md:text-3xl text-foreground">
              Find Your Spot
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">
            {seating.map((seat) => (
              <div key={seat.id} className="bg-card p-6 rounded-sm border border-border">
                <div className="flex items-center justify-between mb-3">
                  <h3 className="font-serif text-lg text-foreground">{seat.name}</h3>
                  <span className="text-sm text-muted-foreground">{seat.seats} seats</span>
                </div>
                <p className="text-sm text-muted-foreground">{seat.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-16 md:py-24 bg-primary text-primary-foreground">
        <div className="container mx-auto px-4 text-center">
          <h2 className="font-serif text-3xl md:text-4xl mb-6">
            Reserve at The Bar
          </h2>
          <p className="text-primary-foreground/80 text-lg max-w-xl mx-auto mb-10">
            Secure your spot for an evening of exceptional cocktails and conversation.
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
